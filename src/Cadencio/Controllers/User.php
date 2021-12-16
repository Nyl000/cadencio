<?php namespace Cadencio\Controllers;

use Cadencio\Application;
use Cadencio\Exceptions\ApiNotFoundException;
use Cadencio\Exceptions\ApiUnprocessableException;
use Cadencio\Models\NotificationModel;
use Cadencio\Models\UserModel;
use Cadencio\Models\UserOptionModel;
use Cadencio\Services\HookHandler;
use Cadencio\Services\Utils;
use Cadencio\Services\Security\ProviderInterface;

use Firebase\JWT\JWT;
use Ratchet\App;

class User extends RestController
{


    public function init()
    {
        $this->setModel(new UserModel());
    }

    protected function getLinkReset($hash) {
        return BASE_URL.'confimreset/'.$hash;
    }

    public function postReset()
    {
        $body = $this->getRequest()->getJsonBody();
        if (!isset($body->email)) {
            throw new ApiUnprocessableException('Missing email');
        }
        $user = $this->getModel()->getOneWithoutAdminCheck($body->email, 'email');
        if ($user) {
            $hash = hash('SHA256', uniqid());
            $this->getModel()->patch($user['id'], ['hash' => $hash]);
            Utils::sendMail($body->email, 'Reset password confirmation', "
            
                <p>Hello</p>
                <p>Someone (Probably you) asked to reset your password. Please follow <a href=\"".getLinkReset($hash)."\">this link</a> to reset your password. 
                If you cannot click on the link, copy paste the link below: </p>
                <p>{$baseUrl}/confirmreset/$hash</p>
                <p>If you didn't ask a password reset, please ignore this message.</p>
            
            ");
            Application::$instance->getCurrentUserModel()->writeUserLog('INFO', 'Password Renew Asked', $user['id']);
            

        }
    }

    public function postResetpassword()
    {
        $body = $this->getRequest()->getJsonBody();
        if (!isset($body->hash)) {
            throw new ApiUnprocessableException('Missing hash');
        }
        if (!isset($body->password)) {
            throw new ApiUnprocessableException('Missing password');
        }
        $user = $this->getModel()->getOneWithoutAdminCheck($body->hash, 'hash');
        if (isset($user['id'])) {
            $password = hash('SHA256', $body->password);
            $hash = hash('SHA256', uniqid());
            $this->getModel()->patch($user['id'], ['password' => $password, 'hash' => $hash]);
            Application::$instance->getCurrentUserModel()->writeUserLog('INFO', 'Password Renewed', $user['id']);

            return true;
        } else {
            throw new ApiNotFoundException();
        }
    }

    public function getTemptoken()
    {
        return $this->auth->secure(function () {
            $hashedPwd = $this->getModel()->getHashedPassword(Application::$instance->getCurrentUserId());
            $nonce = md5(uniqid());
            $payload = array(
                "iss" => "http://example.org",
                "aud" => "http://example.com",
                "iat" => time(),
                "exp" => time() + 10,
                'pwd_nonce' => $nonce,
                'pwd' => hash('SHA256', $nonce . $hashedPwd . JWT_PRIV_KEY),
                'user_id' => Application::$instance->getCurrentUserId(),
                'user_model' => md5(get_class(Application::$instance->getCurrentUserModel()))

            );
            return ['token' => JWT::encode($payload, JWT_PRIV_KEY, 'HS256')];
        });
    }


    private function generateLoginSuccessResponse($login, $body)
    {
        if (isset($body->use_jwt) && $body->use_jwt) {
            $nonce = md5(uniqid());
            $payload = array(
                "iss" => "http://example.org",
                "aud" => "http://example.com",
                "iat" => time(),
                "exp" => time() + 60 * 60 * 24,
                'pwd_nonce' => $nonce,
                'pwd' => hash('SHA256', $nonce . hash('SHA256', $body->password) . JWT_PRIV_KEY),
                'user_id' => $login,
                'user_model' => md5(get_class(Application::$instance->getCurrentUserModel()))
            );
            Application::$instance->setCurrentUserId($login);
            Application::$instance->getCurrentUserModel()->writeUserLog('INFO', 'User Logged In');
            return ['status' => 'ok', 'token' => JWT::encode($payload, JWT_PRIV_KEY, 'HS256')];

        }
        return ['status' => 'ok'];
    }

    public function postLogin()
    {
        $body = $this->getRequest()->getJsonBody();

        if (!isset($body->email)) {
            throw new ApiUnprocessableException('Missing email');
        }

        if (!isset($body->password)) {
            throw new ApiUnprocessableException('Missing password');
        }

        $login = $this->getModel()->login(trim($body->email), $body->password);
        if ($login) {
            Application::$instance->setCurrentUserModel($this->getModel());
            return $this->generateLoginSuccessResponse($login, $body);
        }

        $securityProviderHook = HookHandler::getInstance()->getHook('register_security_provider');
        foreach ($securityProviderHook as $hook) {
            $provider = $hook();
            if (!$provider instanceof ProviderInterface) {
                throw new \Exception('security provider must implement the ProviderInterface');
            }
            $login = $provider->getModel()->login(trim($body->email), $body->password);
            if ($login) {
                Application::$instance->setCurrentUserModel($provider->getModel());
                return $this->generateLoginSuccessResponse($login, $body);
            }
        }

        $modelDefault = new UserModel();
        $modelDefault->writeUserLog('NOTICE', 'Wrong login attempt for: <' . trim($body->email) . '>');
        return ['status' => 'nok'];

    }

    public function getChecktoken()
    {
        return $this->auth->secure(function () {
            $user = Application::$instance->getCurrentUserModel()->getOne(Application::$instance->getCurrentUserId());
            return ['status' => 'ok', 'user' => $user];
        });
    }

    public function getMynotifications()
    {
        return $this->auth->secure(function () {

            $model = new NotificationModel();

            return ['status' => 'ok', 'notifications' => $model->getMyUnseen()];
        });
    }

    public function getSelf_options()
    {
        return $this->auth->secure(function () {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'update_self');

            $model = new UserOptionModel();
            if (isset($_GET['name']) && !empty($_GET['name'])) {
                return ['value' => $model->getByUserAndName(Application::$instance->getCurrentUserId(), $_GET['name'])];
            } else {
                return $model->getByUser(Application::$instance->getCurrentUserId());
            }
        });
    }

    public function postProcessEntity($candidate)
    {
        if (isset($candidate->password) && !empty($candidate->password)) {
            $candidate->password = hash('SHA256', $candidate->password);
        } else {
            unset($candidate->password);
        }
        return $candidate;
    }


    public function postSelfoptions()
    {
        return $this->auth->secure(function () {

            $this->abortIfNotAllowed($this->getModel()->getResourceName(), 'update_self');

            $body = $this->getRequest()->getJsonBody();

            if (!isset($body->key)) {
                throw new ApiUnprocessableException('Missing key');
            }
            if (!isset($body->value)) {
                throw new ApiUnprocessableException('Missing value');
            }
            $model = new UserOptionModel();
            return $model->createOrUpdate([
                'key' => $body->key,
                'value' => $body->value,
                'id_user' => Application::$instance->getCurrentUserId(),
            ]);

        });
    }

}
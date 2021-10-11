<?php
namespace Cadencio\Services\Security;

use Cadencio\Models\UserModel;
use Firebase\JWT\JWT as JWTLib;
class Jwt implements ProviderInterface {

    private $model;

    public function __construct() {
        $this->model = new UserModel();
    }
    public function getModel() {
        return $this->model;
    }

    public function test() {

        $token = $this->getBearerToken();
        if ($token) {
            try {
                $decoded = JWTLib::decode($token, JWT_PRIV_KEY, array('HS256'));
            }
            catch(\Exception $e) {
                error_log($e->getMessage());
                return false;
            }
            $userModel =$this->getModel();
            if (md5(get_class($userModel)) !== $decoded->user_model) return false;
            if ($userModel->idExists($decoded->user_id) && $userModel->isActive($decoded->user_id) ) {
                $pwdHashed = $userModel->getHashedPassword($decoded->user_id);
                $trueHash = hash('SHA256',$decoded->pwd_nonce.$pwdHashed.JWT_PRIV_KEY);
                if($trueHash === $decoded->pwd) {
                    return ['id' => $decoded->user_id, 'model' =>$this->getModel()];
                }
            }
        }

        return false;

    }

    /*
     * THANKS TO https://stackoverflow.com/questions/40582161/how-to-properly-use-bearer-tokens
     * for this little helper :)
     */
    private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /*
    * THANKS TO https://stackoverflow.com/questions/40582161/how-to-properly-use-bearer-tokens
    * for this little helper :)
    */
    private function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

}
<?php
namespace Cadencio\Services\Security;

use Cadencio\Models\UserModel;
use Firebase\JWT\JWT as JWTLib;
class JwtInUrl implements ProviderInterface {


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
            $userModel = $this->getModel();
            if (md5(get_class($userModel)) !== $decoded->user_model) return false;
            if ($userModel->idExists($decoded->user_id) &&  $userModel->isActive($decoded->user_id)) {

                $pwdHashed = $userModel->getHashedPassword($decoded->user_id);
                $trueHash = hash('SHA256',$decoded->pwd_nonce.$pwdHashed.JWT_PRIV_KEY);

                if($trueHash === $decoded->pwd) {
                    return ['id' => $decoded->user_id, 'model' =>$this->getModel()];
                }
            }


        }

        return false;

    }



    private function getBearerToken() {
        if (isset($_GET['authtoken'])) {
            return $_GET['authtoken'];
        }
        return null;
    }

}
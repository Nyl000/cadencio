<?php
namespace Cadencio\Services\Security;

use Cadencio\Models\UserModel;
use Firebase\JWT\JWT as JWTLib;
class JwtInUrl implements ProviderInterface {

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
            $userModel = new UserModel();
            if ($userModel->idExists($decoded->user_id)) {

                $pwdHashed = $userModel->getHashedPassword($decoded->user_id);
                $trueHash = hash('SHA256',$decoded->pwd_nonce.$pwdHashed.JWT_PRIV_KEY);

                if($trueHash === $decoded->pwd) {
                    return $decoded->user_id;
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
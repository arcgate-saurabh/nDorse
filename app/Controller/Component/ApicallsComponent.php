<?PHP

App::uses('Component', 'Controller');

class ApicallsComponent extends Component {

    public $apiurl; //= "http://localhost/mobapi/api/";
    public $components = array('Session', "Auth", "Cookie");
    public $controller = null;

    public function initialize(Controller $controller) {
        $this->controller = $controller;
        $this->apiurl =  "https://api.ndorse.net/api/"; //LIVE
//        $this->apiurl = Router::url('/', true) . "api/"; //Local
//        $this->apiurl = "http://52.42.97.9/prod2/api/"; //Stage or prod2
//        $this->apiurl = "https://staging.ndorse.net/api/"; //Stage or prod2 https server for testing
    }

    // function run on init
    function startup(Controller $controller) {
        $this->controller = $controller;
        $this->isLoggedIn();
    }

    public function curlget($method, $data) {
        $action = trim($method);
        $apiurl = $this->apiurl . $action . "?" . http_build_query($data);
        //echo $apiurl;exit;
        $this->log($apiurl, 'debugget');
        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
        $headers[] = "Cookie: " . $this->getToken();
        $cSession = curl_init();
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($cSession, CURLOPT_COOKIESESSION, TRUE);
        curl_setopt($cSession, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cSession, CURLOPT_URL, $apiurl);
        $result = curl_exec($cSession);
        curl_close($cSession);
        return $result;
    }

    public function curlpost($method, $data) {
	//pr($data); exit;
        $isLoggedIn = isset($_SESSION['User']) ? true : false;

        $action = trim($method);
        $apiurl = $this->apiurl . $action;
//        echo $apiurl;
//        die;
        $this->log($apiurl, 'debug');
        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
        $headers[] = "Content-type: application/x-www-form-urlencoded;charset=UTF-8";
        $headers[] = "Cookie: " . $this->getToken();

        $cSession = curl_init();
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($cSession, CURLOPT_COOKIESESSION, TRUE);
        if (strstr($action, 'register.json') || (strstr($action, 'login.json') || strstr($action, 'updatepassword.json') || strstr($action, 'facebookConnect.json') || strstr($action, 'googleConnect.json') || strstr($action, 'linkedinConnect.json') || strstr($action, 'twitterConnect.json')) && !$isLoggedIn) {
            curl_setopt($cSession, CURLOPT_HEADER, true);
            curl_setopt($cSession, CURLOPT_FOLLOWLOCATION, 1);
        }
        
         if (strstr($action, 'ADFSClientLogin.json')){
             if(is_array($data)){
                 
             }
         }
        
        curl_setopt($cSession, CURLOPT_URL, $apiurl);
        curl_setopt($cSession, CURLOPT_POST, true);
        curl_setopt($cSession, CURLOPT_POSTFIELDS, http_build_query($data));
        ob_start();  
		//pr($cSession);		
        //echo "test"; exit;
        curl_exec($cSession);
        $result = ob_get_contents();
//	pr($result); exit;

//        if (!$result->status && $result->msg == 'Token expired') {
//            $renewmethod = 'users/renewsession.json';
//            $renewdata['token'] = $data['token'];
//            $renewdata['apikey'] = $this->apikey;
//            $renewdata = $this->curlget($renewmethod, $renewdata);
//            $this->curlpost($method, $data);
//        }
//        pr($result);
//        exit;
        ob_end_clean();

        curl_close($cSession);
        if ((strstr($action, 'login.json') || strstr($action, 'register.json')) && !$isLoggedIn) {



            return $this->extractToken($result);
        } else {
            if ($action != "getTimelyUpdates.json") {
                $this->checkLoggedinStatus($result);
            }
            return $result;
        }
    }

    public function curlpostADFSLogout($url, $data = array()) {
        $apiurl = $url;
//        echo $apiurl;
//        die;
        $this->log($apiurl, 'debug');
        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
        $headers[] = "Content-type: application/x-www-form-urlencoded;charset=UTF-8";
        $headers[] = "Cookie: " . $this->getToken();

        $cSession = curl_init();
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($cSession, CURLOPT_COOKIESESSION, TRUE);
        curl_setopt($cSession, CURLOPT_URL, $apiurl);
        curl_setopt($cSession, CURLOPT_POST, true);
        curl_setopt($cSession, CURLOPT_POSTFIELDS, http_build_query($data));
        ob_start();
        curl_exec($cSession);
        $result = ob_get_contents();
        pr($result); 
        exit;
        ob_end_clean();
        curl_close($cSession);
    }

    public function extractToken($result) {
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $result));
        $repsonse = json_decode(end($fields));
        $token = '';
        $headers = $this->http_parse_headers($result);

        if (!empty($headers) && isset($headers['Set-Cookie']) && !empty($headers['Set-Cookie']) && is_array($headers['Set-Cookie'])) {
            foreach ($headers['Set-Cookie'] as $key => $val) {
                preg_match('/([^:]+);/m', $val, $match);
                $token = $match[1];
            }
        } else if (isset($headers['Set-Cookie']) && !empty($headers['Set-Cookie'])) {
            $val = $headers['Set-Cookie'];
            preg_match('/([^:]+);/m', $val, $match);
            $token = $match[1];
        }

        $this->Session->write('AUTH_TOKEN', $token);
        return json_encode($repsonse);
    }

    public function getToken() {
        if ($this->Session->check('AUTH_TOKEN')) {
            return $this->Session->read('AUTH_TOKEN');
        } else {
            return;
        }
    }

    public function http_parse_headers($header) {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        $json_reponse = end($fields);
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])/', 'strtoupper("\0")', strtolower(trim($match[1])));
                if (isset($retVal[$match[1]])) {
                    if (!is_array($retVal[$match[1]])) {
                        $retVal[$match[1]] = array($retVal[$match[1]]);
                    }
                    $retVal[$match[1]][] = $match[2];
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }

    public function isLoggedIn() {
        if (!$this->Session->read('User')) {
            $notallow = isset($this->controller->notallow) ? $this->controller->notallow : array();
            $action = $this->controller->request->params['action'];
            if (in_array($action, $notallow)) {
                //$this->Session->setFlash('Your Session has been expired.');
                $this->Session->setFlash('Your Session has been expired.', 'sessionflash', array('class' => 'flash-error'));
                $this->controller->redirect(array("controller" => 'home', 'action' => 'index'));
            }
        }
    }

    public function curlWrap($url, $json, $action) {
        define("ZDAPIKEY", "x65ItAEWtPBq8iJ0uWoI8oeFZKX28oXgnyOAnHhr");
        define("ZDUSER", "rwalvekar@ndorse.net");
        define("ZDURL", "https://ndorse.zendesk.com/api/v2");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_URL, ZDURL . $url);
        curl_setopt($ch, CURLOPT_USERPWD, ZDUSER . "/token:" . ZDAPIKEY);
        switch ($action) {
            case "POST":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "GET":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                break;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);
        $decoded = json_decode($output);
        return $decoded;
    }

    private function checkLoggedinStatus($result) {
        $response = json_decode($result);
        if (isset($response->result->isExpired) && $response->result->isExpired == true) {
            $this->Auth->logout();
            $this->Cookie->delete("portal_cookie");
            $this->Session->setFlash(__($response->result->msg), 'default', array('class' => 'alert alert-warning'));
            $this->controller->redirect(array('controller' => 'client', 'action' => 'login'));
        }
    }

}

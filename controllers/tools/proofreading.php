<?php
namespace Concrete\Package\OunziwProofreading\Controller\Tools;

use Concrete\Core\Package\Package;
use Core;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use \Concrete\Core\Controller\Controller as RouteController;

class Proofreading extends RouteController
{
    protected $requesturl = "http://jlp.yahooapis.jp/KouseiService/V1/kousei";
    protected $token = '';
    protected $appid = '';
    protected $pkg;

    public function on_start() {
        $this->pkg = Package::getByHandle('ounziw_proofreading');
        $u = new User();
        if ($u->checkLogin()) {
            $this->appid = $this->pkg->getConfig()->get('concrete.ounziw_proofreading');
            $this->token = Core::make('token');
        } else {
            exit();
        }
        if ($this->pkg->getConfig()->get('concrete.ounziw_proofreading_ssl')) {
            $this->requesturl = str_replace('http://', 'https://', $this->requesturl);
        }
    }

    // In order to create your own form, use this method.
    public function return_token() {
        echo $this->token->generate('ounziw_proofreading');
    }

    // Render a form via Ajax
    public function render()
    {
        if (empty($this->appid)) {
            echo '<p>' . t('WARNING: Yahoo API Key is not set.') . '</p>';
            exit;
        }
        $data_class = 'div.ccm-page';
        if ($this->pkg->getConfig()->get('concrete.ounziw_proofreading_class')) {
            $data_class = h($this->pkg->getConfig()->get('concrete.ounziw_proofreading_class')) ;
        }
        echo '<p id="getbodytext" data-areaclass="' . $data_class . '"><button type="submit">' . t('Get the Content on this Page') . '</button></p>';
        echo '<form id="proofreading" method="post">';
        echo '<input type="hidden" name="ounziw_proofreading" value="' . $this->token->generate('ounziw_proofreading') . '" />';
        echo '<div class="form-group">
            <textarea id="textdata" class="form-control" name="textdata" rows="7"></textarea>';
        echo '<p>'. t('Length of the content: ') .'<span id="textlen">0</span> &nbsp;&nbsp; '. t('Long contents may result in time out or other errors. Length depends on your server. We recommend up to 3000.') .'</p>';
        echo '</div>
            <button type="submit" name="submit">' . t('Submit') . '</button>
            <div id="yahoosubmit"></div>
        </form>
        ';

        echo '<hr><div id="result"><p>' . t('Proofreading results will be shown here.') . '</p></div>';

        // This is a copyright message. Do not alter or remove this.
        echo '<!-- Begin Yahoo! JAPAN Web Services Attribution Snippet -->
<span style="margin:15px 15px 15px 15px"><a href="https://developer.yahoo.co.jp/about">Web Services by Yahoo! JAPAN</a></span>
<!-- End Yahoo! JAPAN Web Services Attribution Snippet -->';
        // End a copyright message.

        echo '<style>#yahoosubmit{
        float:left;
        left: 20px;
        }
        #loading{
    border:1px dashed #999;
    padding:15px;
    position: relative;
    background-image:url("'. \Core::getApplicationURL() . '/concrete/images/dashboard/sitemap/loading.gif");
    background-size: cover;
    background-color:#FFF;
    filter: alpha(opacity=85);
    -moz-opacity:0.85;
    opacity:0.85;
}
</style>';
    }

    // Data from Ajax
    public function yahooapi()
    {
        if ($this->token->validate("ounziw_proofreading", $_POST["ounziw_proofreading"])) {
            $resultarray = $this->connect_to_yahoo();
        } else {
            $resultarray = array('success' => 0, 'number' => 0, 'data' => array());
        }
        echo json_encode($resultarray, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

    }
    protected function connect_to_yahoo() {

        $app = Core::make('app');
        $request = $app->make(\Concrete\Core\Http\Request::class);

        $data = array(
            "appid" => $this->appid,
            "sentence" => $request->post('textdata'),
            "output" => "xml"
        );
        $data = http_build_query($data, "", "&");

        //header
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: ".strlen($data)
        );

        $context = array(
            "http" => array(
                "method"  => "POST",
                "header"  => implode("\r\n", $header),
                "content" => $data
            )
        );

        $response = file_get_contents($this->requesturl, false, stream_context_create($context));

        $responsexml = simplexml_load_string($response);

        $result_num = count($responsexml->Result);
        $resultarray = array('success' => 1, 'number' => $result_num, 'sentence' => $request->post('textdata'), 'data' => array());
        if($result_num > 0){
            for ($i = 0; $i < $result_num;$i++) {
                $resultarray['data'][] = $responsexml->Result[$i];
            }
        }
        return $resultarray;
    }
}
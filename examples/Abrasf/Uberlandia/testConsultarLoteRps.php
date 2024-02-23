<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 'On');
require_once '../../../bootstrap.php';

use NFePHP\NFSe\NFSe;
use NFePHP\Common\Certificate;
use NFePHP\NFSe\Models\Simpliss\SoapCurl;

$arr = [
    "atualizacao" => "2016-08-03 18:01:21",
    "tpAmb" => 2,
    "versao" => 1,
    "razaosocial" => "LICITANET LICITACOES ELETRONICAS EIRELI",
    "cnpj" => "21280462000180",
    "cpf" => "",
    "im" => "26043500",
    "cmun" => "3170206", //UBERLANDIA
    "siglaUF" => "MG",
    "senha" => "pgj84233",
    "pathNFSeFiles" => "/dados/nfse",
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]
];

$configJson = json_encode($arr);
$contentpfx = file_get_contents('/var/www/html/licitanet/php-nfse/src/Certs/udi/certificado.pfx');

try {
    //com os dados do config e do certificado já obtidos e desconvertidos
    //a sua forma original e só passa-los para a classe 
    $nfse = new NFSe($configJson, Certificate::readPfx($contentpfx, 'pgj84233'));
    //Por ora apenas o SoapCurl funciona com IssNet
    $nfse->tools->loadSoapClass(new SoapCurl());
    //caso o mode debug seja ativado serão salvos em arquivos 
    //a requisicção SOAP e a resposta do webservice na pasta de 
    //arquivos temporarios do SO em sub pasta denominada "soap"
    $nfse->tools->setDebugSoapMode(false);
    
    $protocolo = '5e798c53-ec97-44e0-a048-aaa35966afcf';
    $content = $nfse->tools->consultarLoteRps($protocolo);
    
    header("Content-type: text/xml");
    echo $content;
    
    //echo "<pre>";
    //print_r($response);
    //echo "</pre>";
    
} catch (\NFePHP\Common\Exception\SoapException $e) {
    echo $e->getMessage();
} catch (NFePHP\Common\Exception\CertificateException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}
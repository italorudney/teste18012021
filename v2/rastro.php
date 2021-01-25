<?php

require_once './vendor/autoload.php';


use Dompdf\Dompdf;
use bubbstore\Correios\CorreiosTracking;
use bubbstore\Correios\Exceptions\CorreiosTrackingException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class rastro
{
    private $dadosRastreamento;
    private $codigoRastreamento;

    public function enviaEmail($subject, $body, $pdf)
    {
        $mail = new PHPMailer(false);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'italorudney@gmail.com';
        $mail->Password = '8fdf39italorudney';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->setFrom('italorudney@gmail.com', 'Desenvolvedor Italo Rudney');
        $mail->addAddress('joao.macedo@elastic.fit', 'Joao Macedo');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAttachment($pdf);
        $mail->send();
        echo 'Message has been sent';
    }

    public function rastrearObjeto($codigoRastreio)
    {
        try {
            $tracking = new CorreiosTracking($codigoRastreio);
            $this->dadosRastreamento = $tracking->find();
            $this->codigoRastreamento = $codigoRastreio;
            $result["cabecalho"] = $this->montaCabecalhoEmail();
            $result["corpo"] = $this->montaCorpoEmail();
            $result["pdf"] = $this->montaPdfEmail();
            return $result;
        } catch (CorreiosTrackingException $e) {
            $retorno["cabecalho"] = "Erro";
            throw new Exception($e->getMessage());
        }
    }

    private function montaCabecalhoEmail()
    {
        if ($this->dadosRastreamento['last_status'] == "Objeto entregue ao destinatário") {
            return "Objeto {$this->codigoRastreamento} Entregue";
        } else {
            return "Objeto {$this->codigoRastreamento} Postado";
        }
    }

    private function montaCorpoEmail()
    {
        $html = "";
        foreach ($this->dadosRastreamento['tracking'] as $value) {
            $html.= "<hr>";
            $html.= "<div>";
            $html.= "<div>{$value["timestamp"]}<br>{$value["locale"]}<br>{$value["status"]}</div>";
            $html.= "</div>";
        }
        $html.= "<div><h2>Dados do Envio</h2><h3>Italo Rudney Cavalcante da Graça Silva</h3>Telefone: (82)9 9941-1653<br> Endereço: Rua 56, Graciliano Ramos, Maceió-AL</div>";
        return $html;
    }

    private function montaPdfEmail()
    {
        $html = $this->montaCorpoEmail();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrail');
        $dompdf->render();
        $output = $dompdf->output();
        $dirname = __DIR__ . '/pdf';
        if (!is_dir($dirname)) {
            mkdir($dirname);
        }
        $fileName = "Tracking-{$this->codigoRastreamento}.pdf";
        $dirFile =  __DIR__ . '/pdf/' . $fileName;

        file_put_contents($dirFile, $output);
        return $dirFile;
    }
}

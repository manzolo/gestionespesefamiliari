<?php

namespace Fi\AutenticazioneBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Fi\CoreBundle\Controller\gestionepermessiController;

class AutenticazioneController extends Controller {

    public function writeInfoLog($message) {
        $logger = $this->get('logger');
        $logger->info($message);
    }

    public function homeAction(Request $request) {


        $developmodeurl = "";
        if (in_array($this->get("kernel")->getEnvironment(), array('dev', 'test'))) {
            $developmodeurl = $request->getScriptName();
        }

        $gestionepermessi = new gestionepermessiController();
        $gestionepermessi->setContainer($this->container);
        $utentecorrente = $gestionepermessi->utentecorrenteAction();

        //echo $utentecorrente["id"];

        $operatore = $this->getDoctrine()
                ->getRepository('FiCoreBundle:operatori')
                ->find($utentecorrente["id"]);

        //echo $operatore->getRuoli()->getPaginainiziale();



        $paginainiziale = $operatore ? $operatore->getRuoli()->getPaginainiziale() : "";

        $this->writeInfoLog("pi: " . $developmodeurl . $paginainiziale);

        return urlencode($developmodeurl . $paginainiziale);
    }

    public function getHomeUser(Request $request, $username) {


        $developmodeurl = "";
        if (in_array($this->get("kernel")->getEnvironment(), array('dev', 'test'))) {
            $developmodeurl = $request->getScriptName();
        }

        $operatore = $this->getDoctrine()
                ->getRepository('FiCoreBundle:operatori')
                ->findOneByUsername($username);


        $paginainiziale = $operatore->getRuoli() ? $operatore->getRuoli()->getPaginainiziale() : "";

        return urlencode($developmodeurl . $paginainiziale);
    }

    public function signinAction(Request $request) {
        $ridirezione = urlencode($request->query->get("ridirezione"));
        if (!$ridirezione) {
            $developmodeurl = "";
            if (in_array($this->get("kernel")->getEnvironment(), array('dev', 'test'))) {
                $developmodeurl = $request->getScriptName();
            }

            $percorsorichiesto = urlencode($developmodeurl) . $ridirezione;
        } else {
            $percorsorichiesto = $ridirezione;
        }
        return $this->redirect($this->generateUrl("fi_autenticazione_accedi", array("ridirezione" => $percorsorichiesto)));
    }

    public function ssoAction(Request $request) {
        $this->writeInfoLog("Esegue sso");
        //Si prende l'url al quale fare la ridirezione dai parametri in get
        $ridirezione = urlencode($request->query->get("ridirezione"));
        if (!$ridirezione) {
            $ridirezione = "/";
        }
        //Si prende l'host http
        $percorsosito = $request->server->get('HTTP_HOST');
        //Si chiama SSO per vedere dove inviare la richiesta http
        $indirizzodachiamare = $this->chiamataSso($request);

        //$stringatotaleredirezione = ($ridirezione ? "http://" . $percorsosito . $developmodeurl . "/autenticazione/ok%3Fridirezione=". urlencode($developmodeurl) . $ridirezione : "");
        $stringatotaleredirezione = $this->generateUrl("fi_autenticazione_ok", array("ridirezione" => $ridirezione), true);
        $this->writeInfoLog("Rediretto a $ridirezione");
        //var_dump($ridirezione);exit;
        //Si fa la redirect alla pagina che ci interessa
        $destinationUrl = $indirizzodachiamare . ($ridirezione ? "&redirect=" . urldecode($stringatotaleredirezione) : "");
        //var_dump($destinationUrl);exit;
        return $this->redirect($destinationUrl);
    }

    public function okAction(Request $request) {
        //Si prende il Token SSO
        $ssotoken = $request->query->get('ssoid');

        $this->writeInfoLog("token: " . $ssotoken);
        //Si scrive il token SSO in sessione
        $this->scriviSsoid($ssotoken);

        //Si chiede lo stato all'SSO
        $risposta = $this->checkSso();
        $this->writeInfoLog("risposta: " . $risposta);

        /* fare il parsing XML della risposta per sapere se ok */
        $sso_detail = new SimpleXMLElement($risposta);
        $dettaglio = $sso_detail->LOGININFO[0];
        $utente = $dettaglio->USERNAME;
        $errore = $sso_detail->ERRNUMBER[0];
        $this->writeInfoLog("Utente logged: " . $utente);
        $this->writeInfoLog("Codice errore: " . $errore);

        if (($errore != -1) && ($utente)) {
            $matricola = strtolower($utente->__toString());
            //Se OK si setta in sessione il token di autenticazione symfony (non quello SSO)
            $operatore = $this->getDoctrine()
                    ->getRepository('FiCoreBundle:operatori')
                    ->findOneByUsername($matricola);

            $ruoli = array();

            if ($operatore) {
                if ($operatore->getRuoli()) {
                    if ($operatore->getRuoli()->getIsUser()) {
                        $ruoli[] = 'ROLE_USER';
                    }
                    if ($operatore->getRuoli()->getIsAdmin()) {
                        $ruoli[] = 'ROLE_ADMIN';
                        $ruoli[] = 'ROLE_USER';
                    }
                    if (($operatore->getRuoli()->getIsSuperadmin())) {
                        $ruoli[] = 'ROLE_SUPER_ADMIN';
                    }
                    if (count($ruoli) == 0) {
                        $ruoli[] = 'ROLE_UNDEFINED_USER';
                    }
                } else {
                    $ruoli[] = 'ROLE_UNDEFINED_USER';
                }
            } else {
                $operatore = new \Fi\CoreBundle\Entity\operatori();
                $operatore->setOperatore($matricola);
                $operatore->setUsername($matricola);
                $operatore->setEmail($matricola . "@comune.fi.it");
                $operatore->setPassword($matricola);
                $doctrine = $this->container->get('doctrine');
                $em = $doctrine->getManager();
                $em->persist($operatore);
                $em->flush();

                $ruoli[] = 'ROLE_UNDEFINED_USER';
            }


            $token = new UsernamePasswordToken($operatore, null, 'secured_area', $ruoli);
            //Security secured_area SSO
            $this->get("session")->set('_security_secured_area', serialize($token));
            //Security main FOS
            $this->get("session")->set('_security_main', serialize($token));
            //$this->get("session")->set('utente', $operatore);
            //***** Sto maledetto non vuole funzionare... dopo la redirect perde il token *****
            //$token = new UsernamePasswordToken($utente->__toString(), null, 'secured_area', array('ROLE_USER', 'ROLE_ADMIN'));
            //$this->container->get('security.context')->setToken($token);
            //var_dump($ssotoken);exit;

            $utente = substr($utente, 1);
            $this->writeInfoLog("scrivi nel contesto $utente");
        }
        //E si fa la redirect
        //Si prende l'url al quale fare la ridirezione dai parametri in get
        $ridirezione = $request->query->get("ridirezione");
        /* @var $contesto Symfony\Component\Routing\RequestContext */
        $ref = str_replace("app_dev.php/", "", parse_url($ridirezione, PHP_URL_PATH));

        if ((!$ridirezione) | $ref == "/") {
            $ridirezione = urldecode($this->getHomeUser($request, $matricola));
        }

        $this->writeInfoLog("ridirezione " . $ridirezione);
        return $this->redirect(($ridirezione ? $ridirezione : '/'));
    }

    public function signoutAction(Request $request) {

        //$this->utenteCer();
        //if (isset($_SERVER['PHP_OUT_USER']) && $_SERVER['PHP_OUT_USER']) {
        //    $indirizzodachiamare = "https://accessoconcertificato.comune.fi.it";
        //} else {
        //Si prende l'indirizzo di logout di SSO
        $indirizzodachiamare = $this->logOutSso($request);
        //}
        //Si reindirizza dove viene detto da SSO
        return $this->redirect($indirizzodachiamare);
    }

    //EX fiAutenticazione.class
    function nomeNelLog() {
        $appname = $this->container->getParameter('appname');
        $logger = $this->get('logger');
        $logger->info("nome dell'applicazione: " . $appname);
    }

    // ------------------------------------------------
    // funzione per le chiamate a https con i parametri specifici
    function openHTTPS($url) {
        $httpscall = curl_init();
        curl_setopt($httpscall, CURLOPT_URL, $url);
        curl_setopt($httpscall, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($httpscall, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($httpscall, CURLOPT_HEADER, 0);
        curl_setopt($httpscall, CURLOPT_PROXY, null);
        curl_setopt($httpscall, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        curl_setopt($httpscall, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($httpscall);
        //trigger_error(curl_error($httpscall));
        curl_close($httpscall);
        return $result;
    }

    function chiamataSso(Request $request) {
        $appname = $this->container->getParameter('appname');
        //Gestione accesso esterno con server https://accessoconcertificato.comune.fi.it -> 192.168.2.147
        $trustedIp = array("192.168.2.147","192.168.202.181");
        $ipRequest = $request->server->get('REMOTE_ADDR');
        if (in_array($ipRequest, $trustedIp)) {
            $ssoUrl = "https://accessoconcertificato.comune.fi.it/pwd/ssocdf/cgi-bin/login.cgi?srv=" . $appname;
        } else {
            $ssoUrl = 'https://sso.comune.fi.it/cgi-bin/login.cgi?srv=' . $appname;
        }

        return $ssoUrl;
    }

    function chiamataLogoutSso(Request $request) {
        $appname = $this->container->getParameter('appname');
        //Gestione accesso esterno con server https://accessoconcertificato.comune.fi.it -> 192.168.2.147
        $trustedIp = array("192.168.2.147","192.168.202.181");
        $ipRequest = $request->server->get('REMOTE_ADDR');
        if (in_array($ipRequest, $trustedIp)) {
            $ssoUrl = 'https://accessoconcertificato.comune.fi.it/pwd/ssocdf/cgi-bin/logout.cgi?srv=' . $appname . '&CGISESSID=';
        } else {
            $ssoUrl = 'https://sso.comune.fi.it/cgi-bin/logout.cgi?srv=' . $appname . '&CGISESSID=';
        }

        return $ssoUrl;
    }

    function checkSso() {
        //Si prende il token SSO
        $array_ssoid = $this->get('session')->get('ssoid');
        //$array_ssoid = sfContext::getInstance()->getUser()->getAttribute('ssoid');
        $ssoid = $array_ssoid[0];
        //TODO: LOG
        $this->writeInfoLog("check leggo ssoid: " . $ssoid);
        $ssoUrl = 'https://sso.comune.fi.it/cgi-bin/ssologincheck.cgi?ssoid=';
        //E si invia la richiesta a SSO per vedere se siamo o meno autenticati
        $check = $this->openHTTPS($ssoUrl . $ssoid);
        return $check;
    }

    function logOutSso(Request $request) {
        //In caso di logout si pulisce la sessione del token symfony
        //$this->container->get('security.context')->setToken(null);
        $this->get("session")->set('_security_secured_area', null);
        $this->get("session")->set('_security_main', null);
        $array_ssoid = $this->get('session')->get('ssoid');
        //$array_ssoid = sfContext::getInstance()->getUser()->getAttribute('ssoid');
        $ssoid = $array_ssoid[0];
        $this->get('session')->set('ssoid', '');
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();


        $this->writeInfoLog("logout leggo ssoid: " . $ssoid);
        //$array_ssoid = $this->getUser()->set('ssoid', '');
        //sfContext::getInstance()->getUser()->setAttribute('ssoid', '');
        $appname = $this->container->getParameter('appname');
        $ssoUrl = $this->chiamataLogoutSso($request);
        //'https://sso.comune.fi.it/cgi-bin/logout.cgi?srv=' . $appname . '&CGISESSID=';
        $indirizzo = $ssoUrl . $ssoid;
        //E si pulisce la sessione
        $this->get('session')->clear();
        return $indirizzo;
    }

    function scriviSsoid($ssoid) {
        $array_ssoid = Array($ssoid);

        //$session = new Session();
        $checksso = $this->get('session')->get('ssoid');
        if ($checksso[0] != $array_ssoid[0]) {
            $this->get('session')->set('ssoid', $array_ssoid);
        }

        $this->writeInfoLog("scrivo ssoid: " . $array_ssoid[0]);
        //sfContext::getInstance()->getUser()->setAttribute('ssoid', $array_ssoid);
        //sfContext::getInstance()->getLogger()->info("scrivo ssoid: " . $array_ssoid[0]);
    }

    function isAutorizzato() {
        //TODO: DA RIFARE SYMFONY2
        //$connessione = Doctrine_Manager::connection("mysql://root:mysqlpwd@localhost/dbaccessicodecharge");

        $log = new Log();
        $elencocredenziali = "";
        $pratica = new pratica();

        $conn = Doctrine_Manager::getInstance();
        $connessione = $conn->getConnection('autenticazione');

        $this->utenteCer();

        $utente = (isset($_SERVER['PHP_OUT_USER']) && $_SERVER['PHP_OUT_USER'] ? $_SERVER['PHP_OUT_USER'] : "D" . sfContext::getInstance()->getUser()->getAttribute('utente'));


        /* select * from utente u, livelloutente lu where (u.idLivello = lu.idLivello) and (lu.idApplicativo = 14) and (u.Username = 'D39523') */
        $app_id = $this->container->getParameter('appid_applicativo');
        $q = Doctrine_Query::create($connessione)
                ->select("lu.profilo profilo")
                ->from("Utente u")
                ->leftJoin("u.Livelloutente lu")
                ->where("u.username = ?", $utente)
                ->andWhere("lu.idapplicativo = ?", $app_id);

        sfContext::getInstance()->getLogger()->info($q->getSqlQuery());

        $tutti = $q->fetchArray();

        sfContext::getInstance()->getLogger()->info("utente di isAutorizzato " . $utente);
        /*
          sfContext::getInstance()->getLogger()->info($pratica->estendiArray($_SERVER));
          sfContext::getInstance()->getLogger()->info($pratica->estendiArray($_SESSION));
         */

        if ($tutti) {
            sfContext::getInstance()->getLogger()->info("tutti esiste" . $pratica->estendiArray($tutti));
            sfContext::getInstance()->getUser()->setAuthenticated(true);
            foreach ($tutti as $uno) {
                sfContext::getInstance()->getLogger()->info("profilo aggiunto " . $uno['profilo']);
                sfContext::getInstance()->getUser()->addCredential($uno['profilo']);
                $elencocredenziali .= $uno['profilo'] . "|";
            }

            $log->setUtente($utente);
            $log->setIndirizzoIp($_SERVER['REMOTE_ADDR']);
            $log->setAzione("accesso");
            $log->setParametri($elencocredenziali);
            $log->save();
        }

        //sfContext::getInstance()->getUser()->addCredential("consultatore");
    }

    function utenteCer() {

        $stringatrovato = "";
        $vettoretrovato = array();
        $utentetrovato = "";

        //$pratica = new pratica();

        $stringatrovato = (isset($_SERVER['HTTP_SSL_CLIENT_S_DN_CN']) ? $_SERVER['HTTP_SSL_CLIENT_S_DN_CN'] : "");
        $vettoretrovato = ($stringatrovato ? explode(":", $stringatrovato) : "");

        // sfContext::getInstance()->getLogger()->info($uno['profilo']);


        $utentetrovato = (isset($vettoretrovato[0]) ? $vettoretrovato[0] : "");

        $_SERVER['PHP_OUT_USER'] = $utentetrovato;
        //TODO:LOG
        //sfContext::getInstance()->getLogger()->info("Passo da utenteCer");
    }

}

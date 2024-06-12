<?php

class ValidaComponentes { 
    
    public $valorteste="";   
   
    public function __construct($valorteste){

      //$this->valorComponente = $valorteste;

        if($valorteste != ""){

          $this->NFe = new COM("NFeX.spdNFeX"); 
          $this->NFCe = new COM("NFCeX.spdNFCeX"); 
          $this->CTe = new COM("CTeX.spdCTeX"); 
          $this->MDFe = new COM("MDFeX.spdMDFeX");   
          $this->GNRe = new COM("GnreX.spdGnreX");

        }else{
            echo "Nenhum componente selecionado";
        }        
     }

   

     public function retornaDFeValicao($Componente,$cnpj,$cnpjsh,$tokensh){
       
        $validaCampos = $this->validaCampos($cnpj,$cnpjsh,$tokensh);

        if($validaCampos == ""){
        try {        
        return $this->RetornoValidacao($Componente,$cnpj,$cnpjsh,$tokensh);    

        }catch(Exception $e){
            echo $e;
        }
        }else{
        echo $validaCampos;
        }

    }

    public function RetornoValidacao($Componente,$cnpj,$cnpjsh,$tokensh){
        
        if($Componente == "Reinf" || $Componente == "Esocial" || $Componente  == "Sped"){
            
              return $this->retornaDFevalidacaoAPI ($Componente,$cnpj,$cnpjsh,$tokensh);     

        }else if($Componente == "NFe"|| $Componente == "NFCe"|| $Componente == "CTe"|| $Componente == "MDFe" || $Componente == "GNRe" ){
           
              return $this ->retornaDFeValidacaoComponentes($Componente,$cnpj,$cnpjsh,$tokensh);

        }else{
            echo $Componente." : Falha ao OBTER validacao da API ou Componentes";
        }     
    }

    public function criaMensagemdeSucessoAPI($Componente,$cnpj,$cnpjsh,$codigo){
        
        if($codigo == "50" || "10000"){
          return $Componente. " : Licença valida para o cliente CPFCNPJ: $cnpj CPFCNPJSH: $cnpjsh";    
        }else{
            echo "Erro ao criar mensagem de SUCESSO da LICENÇA por API";
        }
    }

    public function retornaDFeValidacaoComponentes($Componente,$cnpj,$cnpjsh,$tokensh){
          
        $this -> configurarcomponente($Componente,$cnpj,$cnpjsh,$tokensh);
        $this -> deletaArquivosLicenses($Componente); 
        $retorno = $this -> requisitaMetodoComponente ($Componente);        
       
         if($retorno != ""){
          $ultimaLinhaLicenseX = $this -> retornaUltimaLinhaLicenseX($Componente);
          return $Componente.": ".$ultimaLinhaLicenseX;
          }else{
            echo " \n Metodo requisitaMetodoComponente : Erro ao obter retorno  ".$retorno;
          }
    }

    
    public function configurarcomponente($valorComponente,$cnpjCliente,$cnpjsh,$tokensh){

    try{

        $this->$valorComponente->ConfigurarSoftwareHouse($cnpjsh,$tokensh);
        $this->$valorComponente->Ambiente = 2;            
        $this->$valorComponente->CNPJ = $cnpjCliente;
        $this->$valorComponente->NomeCertificado = "CN=TECNOSPEED NEGOCIOS LTDA:29062609000177, OU=Certificado PJ A1, O=ICP-Brasil, C=BR, S=PR, L=Maringa, E=, SE=6D 8C 22 11 18 4B EF 8C";
        $this->$valorComponente->ArquivoServidoresHom="C:/Program Files/TecnoSpeed/$valorComponente/arquivos/$valorComponente"."ServidoresHom.ini";
        $this->$valorComponente->ArquivoServidoresProd="C:/Program Files/TecnoSpeed/$valorComponente/arquivos/$valorComponente"."ServidoresProd.ini";
        $this->$valorComponente->DiretorioEsquemas="C:/Program Files/TecnoSpeed/$valorComponente/arquivos/Esquemas/";
        $this->$valorComponente->DiretorioTemplates="C:/Program Files/TecnoSpeed/$valorComponente/arquivos/templates/";
        $this->$valorComponente->DiretorioLog="C:\\xampp\htdocs\\Logs\\Log$valorComponente Hom";
        $this->$valorComponente->DiretorioLogErro="C:\\xampp\htdocs\\Logs\\Log$valorComponente Erro";  
        
        if($valorComponente != "GNRe"){
            $this->$valorComponente->UF = "PR";   
        }
       
        if($valorComponente == "CTe")
        {
            $this->$valorComponente->VersaoManual=8;  
        }

        if($valorComponente == "MDFe")         
        {
        $this->$valorComponente->VersaoManual="3";  

        }if($valorComponente == "GNRe")
        {
            $this->$valorComponente-> ArquivoCodigosHom = "C:\\Program Files\\TecnoSpeed\\GNRe\\Arquivos\\gnreCodigosHom.ini";
            $this->$valorComponente-> ArquivoCodigosProd = "C:\\Program Files\\TecnoSpeed\\GNRe\\Arquivos\\gnreCodigosProd.ini";
            $this->$valorComponente-> DiretorioXmlGnre = "C:\\xampp\\htdocs\\Logs\\Log GNRe";
        }
        }catch(Exception $e){
            echo $e;
        }
     }

    //função que recebe como parametro nome de um dos componentes  para chamar o status (spdNFe,spNFCe,spdCTe,spdMDFe)
    public function requisitaMetodoComponente($Componente){     
    try{  
        if($Componente == "MDFe"){
        return  $this -> $Componente -> StatusDoServicoMDFe();     

        }else if ($Componente == "GNRe"){   

          $dataHoje = date('Y-m-d');    
          $tx2 = "formato=tx2\nINCLUIR\nc33_dataPagamento=$dataHoje\nINCLUIRITEM\nc26_produto=3\nc25_detalhamentoReceita=000012\nINCLUIRCAMPOEXTRA\nc39_camposExtras_Codigo=50\nc39_camposExtras_Valor=Observacao da guia\nSALVARCAMPOEXTRA\nSALVARITEM\nSALVAR";
          $caminhoXmlbase = "C:\\xampp\\htdocs\\xmlgnre\\xmlbaseEnvio.xml";

        return $this -> $Componente -> EnviarGuia ($caminhoXmlbase,$tx2,"ST");

        }else{
        return   $this -> $Componente -> StatusDoServico();

        }
        }catch (Exception $e){
            echo $Componente." : ERRO ao consultar StatusDoServico / Verifique os daddos / Consulte os arquivos de LOG";
        }
    }
     

    function deletaArquivosLicenses($Componente){

        $arquivo = $this -> selecionaArqlicense($Componente);
        
      if(file_exists($arquivo)){
            
        //deleta o arquivo de .dat
         $this->deletaArq($arquivo); 
        //deleta o arquivo licenseX
         if($Componente == "NFe"){
         $this->deletaArq("C:\\xampp\\htdocs\\Logs\\LogNFe Hom\\LicenseLog\\LicenseX.log");
       
         }else{
         $this->deletaArq("C:\\xampp\\apache\bin\\Log\\LicenseLog\\LicenseX.log");        
         }
            
      }else{
        echo "Erro ao deletar license (Arquivo nao: ".$Componente;
      }

    }

    public function deletaArq($dirArquivo){

         if (file_exists($dirArquivo)) {
              unlink($dirArquivo);
   
        } else {
            echo "O arquivo:\n  $dirArquivo   \n:não existe para ser deletado";
        }       
    }

    public function selecionaArqlicense($Componente){

        $dirlicenseNFe = "C:\\xampp\apache\bin\\spdLicenseNFe.dat";
        $dirlicenseNFCe = "C:\\xampp\apache\bin\\spdLicenseNFCe.dat";
        $dirlicenseMDFe = "C:\\xampp\apache\bin\\spdLicenseMDFe.dat";
        $dirlicenseCTe = "C:\\xampp\apache\bin\\spdLicenseCTe.dat";
        $dirlicenseGNRe = "C:\\xampp\apache\bin\\spdLicenseGNRe.dat";
        
        if ($Componente == "NFe"){
            return $dirlicenseNFe;
        }else if($Componente =="NFCe"){
            return $dirlicenseNFCe;
        }else if ($Componente == "MDFe"){
            return $dirlicenseMDFe;
        }else if ($Componente == "CTe"){
            return $dirlicenseCTe;
        }else if ($Componente == "GNRe") {
            return $dirlicenseGNRe;
        } else {
            echo "Erro ao selecionar arquivo SPDLICENSE.DAT - Documento : $Componente";
        }
    }
    
    // função que recebe diretorio do arquivo em nosso ambiente php e disponibiliza transmissão para download
    public function baixaArq($dirArquivo,$nomeaArquivoZip){

    if (file_exists($dirArquivo)) {

    header('Content-Type: application/zip');
    header("Content-Disposition: attachment;filename=$nomeaArquivoZip"); 
    header('Content-Length: ' . filesize($dirArquivo.$nomeaArquivoZip));
  
    readfile($dirArquivo); // realizad download
    exit;

    } else {
    echo 'Arquivo não encontrado para fazer Download';
    }
    }


    //função que recebe o local do arquivo que queremos zipar, e o nome do noso arquivo zip que sera criado
    public function zip_arquivos($arquivo,$arquivo_zip) {

    if(file_exists($arquivo)){
    // cria um novo objeto ZipArchive
         $zip = new ZipArchive();

    // abre o arquivo ZIP para escrita
         if ($zip->open($arquivo_zip, ZIPARCHIVE::CREATE) !== TRUE) {
        echo "Erro: Falha ao abrir o arquivo ZIP.";
        exit;
    }
   // adiciona o arquivo PHP ao arquivo ZIP
        if ($zip->addFile($arquivo)!== TRUE) {
        echo "Erro: Falha ao adicionar o arquivo ao ZIP.";
        $zip->close();
        exit;
        }
    }else{
        echo  " Arquivo de license não existe (Falha no zip)";
    }
    }


    function selecionaURLapi($componente){

        $url="";

        if($componente == "Esocial"){  
          return  $url = "https://api.tecnospeed.com.br/esocial/v1/evento/enviar/tx2";
        }else if($componente == "Reinf"){
           return $url = "https://api.tecnospeed.com.br/reinf/v2/evento/enviar/tx2";
        }else if($componente == "Sped"){
           return $url = "https://api.tecnospeed.com.br/sped-fiscal/iniciar";
        }else{
            echo "Erro ao montar URL da API\n";
        }
    }

    function selecionaBody($componente,$cnpj,$cnpjsh){

          if($componente == "Esocial"){  
          return $body_tx2 = "cpfcnpjtransmissor=$cnpjsh\ncpfcnpjempregador=$cnpj\nidgrupoeventos=2\nversaomanual=S.01.02.00\nambiente=2\nINCLUIRS2240\nindRetif_4=1\nnrRecibo_5=\ntpAmb_6=2\nprocEmi_7=1\nverProc_8=1.0\ntpInsc_10=1\nnrInsc_11=29062609\ncpfTrab_13=03423068118\nmatricula_15=151612158\ncodCateg_85=\ndtIniCondicao_18=2021-06-23\nINCLUIRINFOAMB_153\nlocalAmb_90=1\ndscSetor_91=escritorio\ntpInsc_92=1\nnrInsc_93=08187168000160\nSALVARINFOAMB_153\ndscAtivDes_22=pressão do trabalho\nobsCompl_84=\nINCLUIRAGNOC_150\ncodAgNoc_94=09.01.001\ndscAgNoc_95=\ntpAval_96=2\nintConc_97=\nlimTol_98=\nunMed_99=\ntecMedicao_100=\nutilizEPC_101=0\neficEpc_102=\nutilizEPI_103=0\nmedProtecao_104=\ncondFuncto_105=\nusoInint_106=\nprzValid_107=\nperiodicTroca_108=\nhigienizacao_109=\nSALVARAGNOC_150\nINCLUIREPI_151\ndocAval_110=\neficEpi_112=S\nSALVAREPI_151\nINCLUIRRESPREG_152\ncpfResp_86=53211347070\nideOC_88=1\ndscOC_89=\nnrOC_73=12586766997\nufOC_74=PR\nSALVARRESPREG_152\nSALVARS2240";
          }else if($componente == "Reinf"){
           return $body_tx2 = "cpfcnpjtransmissor=$cnpjsh\ncpfcnpjempregador=$cnpj\nversaomanual=2.1.02\nambiente=2\nemitirorgaopublico=0\nINCLUIRR4020\nindRetif_3=1\nperApur_5=2023-10\ntpAmb_6=2\nprocEmi_7=1\nverProc_8=1.0\ntpInsc_9=1\nnrInsc_10=12521450\ntpInscEstab_12=1\nnrInscEstab_13=10710409000155\ncnpjBenef_14=12521450000110\nnmBenef_15=ALEXANDRE BUENO ZAPATERRA\nINCLUIRIDEPGTO_100\nnatRend_17=10002\nINCLUIRINFOPGTO_150\ndtFG_19=2023-10-17\nvlrBruto_20=3000,00\nindJud_24=N\nvlrBaseIR_26=3000,00\nvlrIR_27=45,00\nvlrBaseAgreg_28=3000,00\nvlrAgreg_29=139,50\nSALVARINFOPGTO_150\nSALVARIDEPGTO_100\nSALVARR4020";
          }else if($componente == "Sped"){
          return $body = "data_inicio=01-08-2023&data_fim=22-08-2023&cnpj_emissor=$cnpj&cnpj_sh=$cnpjsh&arquivo=ArquivoSPD.txt";
          }else{
              echo "Erro ao montar BODY da API\n";
          }

    }


    public function selecionaHeader($componente,$cnpj,$cnpjsh,$tokensh){
        
       $tokenJWT="";
       if($componente == "Esocial"){         
            return $header = "content-type: text/tx2\ncnpj_sh:$cnpjsh\ntoken_sh:$tokensh";
             
           }else if($componente == "Reinf"){
            $requestToken = $this -> validaAPI("https://api.tecnospeed.com.br/reinf/v2/auth","cnpj_sh=$cnpjsh&token_sh=$tokensh","Content-Type: application/x-www-form-urlencoded");               
                 if(isset($requestToken['token'])){
                 $tokenJWT = $requestToken['token'];
                 } 
            return $header = "content-type: text/tx2\nauthorization:$tokenJWT\nempregador:$cnpj";

           }else if($componente == "Sped"){
            return "cnpj_sh:$cnpjsh\ntoken_sh:$tokensh\ncnpj_emissor:$cnpj\narquivo:ArquivoSPD.txt\nContent-Type: application/x-www-form-urlencoded";
           }else{        
            
             echo "Erro ao montar Header da API\n";
            
        } 
    }


    public function preencheAPI($componente,$cnpj,$cnpjsh,$tokensh){
        
        $url = $this -> selecionaURLapi($componente);
        $body = $this -> selecionaBody($componente,$cnpj,$cnpjsh); 
        $header = $this -> selecionaHeader($componente,$cnpj,$cnpjsh,$tokensh); 

        return  $this -> validaAPI ($url,$body,$header);   

    }

    public function retornaDFevalidacaoAPI($Componente,$cnpj,$cnpjsh,$tokensh){
      
        $array = $this -> preencheAPI($Componente,$cnpj,$cnpjsh,$tokensh);      
        
        if($Componente == "Esocial" || $Componente == "Reinf"){
            if(isset($array['data']['status_envio']['codigo'])){
                $codigo  = $array['data']['status_envio']['codigo'];
                return $this->criaMensagemdeSucessoAPI ($Componente,$cnpj,$cnpjsh,$codigo);                

            }else{
                echo $Componente." : Não foi possivel obter o codigo de retorno da API";
            }

        }else if($Componente == "Sped"){
            if(isset($array['data']['status']['codigo'])){
                $codigo = $array['data']['status']['codigo'];
                return $this->criaMensagemdeSucessoAPI ($Componente,$cnpj,$cnpjsh,$codigo); 
          
            }else{
                echo $Componente. " : Não foi possivel obter o código de retorno da API";
            }

        }
    }


    public function validaAPI($url,$body,$header){ 

        $curl = curl_init();   

        curl_setopt_array($curl,array(
            CURLOPT_URL =>$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS > 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$body,
            CURLOPT_HTTPHEADER => array(
                $header
            ),
        ));

       $response = curl_exec($curl);

       curl_close($curl);       

       return json_decode($response,1);;
        
    }


    function retornaUltimaLinhaLicenseX ($Componente){
        // o método lê o arquivo de LIcenseX.log criado pelo componente e traz a ultima linha do arquivo com a informação sobre a licença valida ou não
        $arquivo="C:\\xampp\\apache\bin\\Log\\LicenseLog\\LicenseX.log";

        if($Componente == "NFe"){
            $arquivo = "C:\\xampp\\htdocs\\Logs\\LogNFe Hom\\LicenseLog\\LicenseX.log";        
        }

        $linha=$this->lerUltimaLinha($arquivo);  
        return $this->extraiConteudo($linha);
    }


    public function baixaLicenseDAT ($componenteselec){

        //diretorio onde cria o zip para ser baixado 
        $dirArquivosZip = 'C:\xampp\htdocs\LicenseDatDownload\\'; 
        //gera nome md5 auto para o arquivo zip
        $nomeaArquivo = md5(time()) . '.zip'; 

        $license_selecionada = $this->selecionaArqlicense($componenteselec);

        $this->zip_arquivos($license_selecionada,$dirArquivosZip.$nomeaArquivo);
    
        if (file_exists($dirArquivosZip.$nomeaArquivo)){
            $this->baixaArq($dirArquivosZip.$nomeaArquivo,$nomeaArquivo);
        }

    }

    // essa função ta bugando quando eu chamo NFCE ta baixando o licenseX log de NFE
    public function baixaLicenseX ($componenteselec){
        //diretorio onde cria o zip para ser baixado 
        $dirArquivosZip = 'C:\xampp\htdocs\LicenseXDownload\\'; 
        //gera nome md5 auto para o arquivo zip
        $nomeaArquivo = md5(time()) . '.zip'; 

       // $arquivo="C:\\xampp\\apache\bin\\Log\\LicenseLog\\LicenseX.log";
        if($componenteselec != "NFe"){
            $arquivo ="C:\\xampp\\apache\bin\\Log\\LicenseLog\\LicenseX.log";           
        }else{ 
            $arquivo = "C:\\xampp\\htdocs\\Logs\\LogNFe Hom\\LicenseLog\\LicenseX.log";
        }

        $this->zip_arquivos($arquivo,$dirArquivosZip.$nomeaArquivo);
    
        if (file_exists($dirArquivosZip.$nomeaArquivo)){
            $this->baixaArq($dirArquivosZip.$nomeaArquivo,$nomeaArquivo);
        }

    }
    
    public function validaCampos($cnpj,$cnpjsh,$tokensh){      
          
        if($cnpj == ""){
            return "Preencha a informação de CNPJ Cliente!";       
        }
        if($cnpjsh==""){
            return "Preencha a informação de CNPJ Software House!";
        }
        if($tokensh==""){
           return "Preencha a informação de Token Software House";
        }
    }


    function lerUltimaLinha($nomeArquivo) {
        // Abre o arquivo no modo de leitura
        $arquivo = fopen($nomeArquivo, "r");      
        // Verifica se o arquivo foi aberto com sucesso
        if ($arquivo === false) {
          return "Erro ao abrir o arquivo.";   
        }     
        // Inicializa uma variável para armazenar a última linha
        $ultimaLinha = null;
        // Itera sobre cada linha do arquivo
        while (($linha = fgets($arquivo)) !== false) {
          // Atualiza a última linha com a linha atual
          $ultimaLinha = $linha;
        }      
        // Fecha o arquivo
        fclose($arquivo);      
        // Retorna a última linha (ou a mensagem de erro se o arquivo estiver vazio)
        return $ultimaLinha ? trim($ultimaLinha) : "O arquivo está vazio.";

      }
         

    
    function extraiConteudo ($linha){
        $posicaoLicenca = strpos(utf8_encode(strftime($linha)), "Licença");
        if ($posicaoLicenca !== false) {
        // Extrai a substring a partir da palavra "Licença" (incluindo a própria palavra)
        $textoAposLicenca = substr($linha, $posicaoLicenca);    
        // Exibe o resultado
        return  utf8_encode(strftime($textoAposLicenca));  // Saída: Licença valida para o cliente CPFCNPJ: 29062609000177 CPFCNPJSH: 29062609000177
        } else {
        //echo "License não encontrada, por favor verifique o LicenseX.log";
        }
    }

}

unset($Componente); 

?>

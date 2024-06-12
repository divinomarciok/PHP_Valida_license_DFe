<!DOCTYPE html>
<html>
     <head>
       <meta charset="utf-8">       
       <link href="\dashboard\stylesheets\style.css"  type="text/css" rel="stylesheet"/>

      <script>
       
       function validaCampos(){

        var cnpjCliente = document.getElementById('idcnpj').value; 

        if(cnpjCliente == ""){
          alert("Preencha o campo CNPJ Cliente");
        }
        var cnpjSh = document.getElementById('idcnpjsh').value;
        if(cnpjSh == ""){
          alert("Preencha o campo CNPJ Software House");
        }
        var tokenSh = document.getElementById('idtokensh').value;
        if(tokenSh == ""){
          alert("Preencha o campo de Token Software House");
        }
       }
      
    function confirmaValidacao() {

       var cnpjCliente = document.getElementById('idcnpj').value; 
       var cnpjSh = document.getElementById('idcnpjsh').value;

       var confirmacao = window.confirm("Deseja realmente validar a licença?\nCNPJ : "+cnpjCliente +"\nCNPJ SH : "+cnpjSh);

       if (confirmacao) {
       document.getElementById("confirmacao").value = "sim";
       return true;
       }else {
        return false;
       }

      }
      
      </script>
       
       <title>Valida Licença HOMO</title>
       
    </head>
<?php 
   //error_reporting(0); // oculta erros de WARNING e E-NOTICE 
    require 'componenteclass_hom.php';
    include('protect.php');

    $xml = "";  
    $returnoValidacao = "";    
    
    
     if(isset($_POST['submitcomponente'])){

      $cnpjCliente = $_POST['cnpjcliente'];
      $cnpjsh = $_POST['cnpjsh'];
      $tokensh = $_POST['tokensh'];       
      $Componente=$_POST['documentos'];      
      $validacao = new ValidaComponentes($Componente); 


      if($_POST['confirmacao']=="sim"){    
        try{

        $returnoValidacao = $validacao -> retornaDFeValicao($Componente,$cnpjCliente,$cnpjsh,$tokensh);

        }catch(Exception $e){
         echo $e;
        }

      }
      
     }   


    if(isset($_POST['baixarlicensedat'])){

      $Componente=$_POST['documentos'];        
    
      if ($Componente != ""){
      $validacao = new ValidaComponentes($Componente); 
      try{
        
      $validacao->baixaLicenseDAT($Componente);        
                
      }catch (Exception $e){
      echo $e;
      }
      }else {
      echo "Nenhum componente selecionado";
      }

     }


     if(isset($_POST['baixarlicenseX'])){

      $Componente=$_POST['documentos'];        
    
      if ($Componente != ""){
      $validacao = new ValidaComponentes($Componente); 
      try{
        
      $validacao->baixaLicenseX($Componente);        
                
      }catch (Exception $e){
      echo $e;
      }
      }else {
      echo "Nenhum componente selecionado";
      }

     }
   
?>
<body>
<!--<form action="index_hom.php" method="post" enctype="multipart/form-data" onsubmit="return confirmaValidacao();">  -->

<form action="index_hom.php" method="post" enctype="multipart/form-data">  
<input type="hidden" id="confirmacao" name="confirmacao" value="">
 
  <div id="container">
     <label>CNPJ Cliente</label><br><input type="text" title="CNPJ SOFTWARE HOUSE" value ="" id="idcnpj" name="cnpjcliente"/><br>
     <label>CNPJ Software House</label><br><input type="text" title="CNPJ SOFTWARE HOUSE" value ="" id="idcnpjsh" name="cnpjsh"/><br>
     <label>Token Software House</label><br><input type="text" title="TOKEN SOFTWARE HOUSE" value="" id="idtokensh" name="tokensh"/><br>
  </div>
  
  <div id="opcaodoc">
     <input type="radio"name="documentos" value="NFe">
     <label for="NFe">NF-e</label>
     <input type="radio"name="documentos" value="NFCe">
     <label for="NFCe">NFC-e</label>
     <input type="radio"name="documentos" value="MDFe">
     <label for="MDFe">MDF-e</label>
     <input type="radio"name="documentos" value="CTe">
     <label for="CTe">CT-e</label>
     <input type="radio"name="documentos" value="GNRe">
     <label for="GNRe">GNR-e</label>
     <input type="radio"name="documentos" value="Esocial">
     <label for="Esocial">eSocial</label>
     <input type="radio"name="documentos" value="Reinf">
     <label for="Reinf">EFD-Reinf</label>     
     <input type="radio"name="documentos" value="Sped">
     <label for="Sped">Sped</label>  
  </div>

  <div id="textbox">
    <!--
           <input type="submit" value="Validar License" name="submitcomponente2"/> 
    -->
    <input type="submit" value="Validar License" name="submitcomponente" onclick="confirmaValidacao()"/>  
    <input type="submit" value="Baixar License.DAT" name="baixarlicensedat"/> 
    <input type="submit" value="Baixar LicenseX" name="baixarlicenseX"/> 

  </div>
  <div id="result">
  <label>Retorno</label><br><input type="text" size="86" value="<?php echo $returnoValidacao ?>" id="return" name="returnValidacao"/><br>
  </div>
  <p>
    <a href="logout.php">Sair</a>
  </p>
</form>

</body>
</html>
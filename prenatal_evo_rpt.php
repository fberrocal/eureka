<?php
// Require composer autoload
require_once __DIR__ . '/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();  /*----Spreadsheet object-----*/
$Excel_writer = new Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet();
// '/"/',    '/\'/', 
// ,'/`/','/#/','/%/','/>/','/</','/!/','/./','/[ -]+/','/]/','/\*/','/\$/','/;/','/:/','/\?/','/\^/','/{/','/}/','/\/'     
$caracteres = array('/\s\s+/','/-/','/\+/','/=/');

require_once('database.class.php');
$conn = new Database();
// Create an instance of the class:
//$mpdf = new \Mpdf\Mpdf();
//$mpdf->setFooter('{PAGENO}');
if(!isset($_POST['idsede'])){
    $idsede='';
}else{
    $idsede=$_POST['idsede'];
}

$parts = explode('-', $_POST['fechaini']);
$fechaini  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$parts = explode('-', $_POST['fechafin']);
$fechafin  = $parts[2].'/'.$parts[1].'/'.$parts[0];

$fechaini.=' 00:00:00.000';
$fechafin.=' 23:59:59.997';

$cabecera="
SELECT 'IDADMINISTRADORA' AS DESCCAMPO
UNION ALL
SELECT 'RAZONSOCIAL' AS DESCCAMPO
UNION ALL 
SELECT 'TIPO_DOC' AS DESCCAMPO
UNION ALL
SELECT 'IDAFILIADO' AS DESCCAMPO
UNION ALL
SELECT 'PNOMBRE' AS DESCCAMPO
UNION ALL
SELECT 'SNOMBRE' AS DESCCAMPO
UNION ALL
SELECT 'PAPELLIDO' AS DESCCAMPO
UNION ALL
SELECT 'SAPELLIDO' AS DESCCAMPO
UNION ALL
SELECT 'FNACIMIENTO' AS DESCCAMPO
UNION ALL
SELECT 'EDAD' AS DESCCAMPO
UNION ALL
SELECT 'SEXO' AS DESCCAMPO
UNION ALL
SELECT 'DIRECCION' AS DESCCAMPO
UNION ALL
SELECT 'TELEFONORES' AS DESCCAMPO
UNION ALL
SELECT 'GRUPOETNICO' AS DESCCAMPO
UNION ALL
SELECT 'CONSECUTIVO' AS DESCCAMPO
UNION ALL
SELECT 'IDSEDE' AS DESCCAMPO
UNION ALL
SELECT 'SEDE' AS DESCCAMPO
UNION ALL
SELECT 'FECHA' AS DESCCAMPO
UNION ALL
SELECT 'CLASEPLANTILLA' AS DESCCAMPO
UNION ALL
UNION ALL
SELECT 'IDMEDICO' AS DESCCAMPO
UNION ALL
SELECT 'MEDICO' AS DESCCAMPO
UNION ALL
select DESCCAMPO from mpld where CLASEPLANTILLA='EVOPRECO' and campo in('ACOMP', 'PARENT' , 'DIR' , 'TEL' , 'INSTITU' ,
 'TER' , 'DTSCONSULTA' , 'MOTCONSULTA' , 'ENFACTUAL' , 'HEMOCLASIF' , 'EDGES' , 'FEINGPRO' , 'TPOCTRL' , 'LEEESC' , 'NROGES' , 
 'NROCONV' , 'RIEBIOT' , 'RBIODESC' , 'HISREPT' , 'PARIDAD' , 'PRERIES' , 'PTOSRBIO' , 'CONDASOC' , 'CONASO' , 'PTOSCASO' , 
 'EMBACTT' , 'EMBACT' , 'PTOSEACT' , 'RIEPSIC' , 'TEMO' , 'HUMO' , 'SINNEU' , 'PTOSPSIC' , 'SOPFAMT' , 'SOPFAM1' , 'SOPFAM2' 
 , 'TIME' , 'ESPA' , 'DINE' , 'PTOSSFAM' , 'RIEPSICPER' , 'PTOSTOT' , 'GESACTT' , 'GESACTST' , 'GESACT' , 'FEULMES' , 
 'FEPROPAR' , 'EGCONFUM' , 'EGCONECO' , 'HEMOC' , 'GRUSAN' , 'RH' , 'SESIBGS' , 'FEGRUSAN' , 'REPLAB' , 'HEMOG' , 
 'RESHEMO' , 'FERES' , 'HEMAT' , 'RESHEMA' , 'FRESHEMA' , 'FROTIS' , 'RESFROT' , 'FRESFROT' , 'CITOQUIM' , 'RESORI' , 
 'FRESCITO' , 'UROCULT' , 'RESURO' , 'FRESURO' , 'ECOOBS' , 'RESECO' , 'FRESECO' , 'TOXO' , 'RESTOX' , 'FRESTOX' , 'SESIBIL' , 
 'GLICEM' , 'RESGLI' , 'FRESGLI' , 'TESTOSULL' , 'RESTEST' , 'FRESTEST' , 'CVATOLGLU' , 'RESCVAGLU' , 'FRESCUGLU' , 'VDRLI' , 
 'RESVDRL' , 'FRESVDRL' , 'SERO' , 'SERODIL' , 'SIFILIS' , 'CLSSIF' , 'ANTSIF' , 'TTOSIFI' , 'TTOSI' , 'TTONO' , 'FEAPTTO' ,
  'DOSIS1' , 'DOSIS2' , 'DOSIS3' , 'FETTOPA' , 'DOSISP1' , 'DOSISP2' , 'DOSISP3' , 'OBSSIF' , 'RETSIF' , 'DOSISRT1' , 
  'DOSISRT2' , 'DOSISRT3' , 'RETSIFP' , 'DOSISRP1' , 'DOSISRP2' , 'DOSISRP3' , 'ELISA' , 'FEELISA' , 'RESELISA' , 'ELISA2' ,
   'FEELISA2' , 'RESELISA2' , 'CARGAV' , 'CARGAVFE' , 'WESTERN' , 'WESFEC' , 'AGSHB' , 'FECAGSH' , 'RESAGSHB' , 'TORCH' ,
    'FETORCH' , 'CITO' , 'RESCITO' , 'FERECIT' , 'COLPOS' , 'RESCOL' , 'FERECOL' , 'CONIZ' , 'FECONIZ' , 'CAUT' , 'FECAUT' ,
     'HIPEMB' , 'CONSEPREP' , 'FECNJPRE', 'CONSEPOSP' , 'FECNJPOS' , 'VARTRAZ' , 'SIGVIT' , 'FECHASV' , 'NOCRTL' , 'EDGEST' , 'PESO' , 'TALLA' , 'IMC' , 
     'PA' , 'PAD' , 'AU' , 'PRE' , 'FCF' , 'MOVFET' , 'BACT' , 'EXAMAMA' , 'PZONAPTO' , 'INTERV' , 'CRA' , 'DESCRA' , 'OJOS' ,
    'DESOJOS' , 'NARIZ' , 'DESNARIZ' , 'BOCA' , 'DESBOCA' , 'OIDOS' , 'DESOIDOS' , 'CYG' , 'DESCYG' , 'TYM' , 'DESTYM' , 
    'ABD' , 'DESABD1' , 'GENUR' , 'DESGENUR' , 'ESPEC' , 'DESESPEC' , 'NEUR' , 'DESNEUR' , 'GLIN' , 'DESGLIN' , 'CPU' , 
  'DESCPU' , 'EGEN' , 'DESEGEN' , 'EXT' , 'DESEXT' , 'PYFA' , 'DESPYFA' , 'EDEM' , 'UBQEDEM' , 'EDEMG' , 'CLAEMB' ,
   'RIE1ERT' , 'RIE4505' , 'GEST' , 'SINRES' , 'OBDES' , 'VICMAL' , 'VVIOL' , 'ETS' , 'EMENT' , 'CAN' , 'CANSEN' , 'SIFGES' ,
    'HIPIND' , 'EDUCA' , 'SEGUIM' , 'VACUNAS' , 'TDTT1' , 'FECHATT1' , 'TDTT2' , 'FECHATT2' , 'INFLUEN' , 'FECHAINF' , 
    'DPTA' , 'FECHADPT' , 'OBSERVACI' , 'PLNINT' , 'MEDORD' , 'HIERRO' , 'CALCIO' , 'ACIDO' , 'EXAM' , 'OTTTO' , 'SIGNOS' ,
     'OBSER' , 'DXPPAL' , 'DESDXP' , 'DXREL1' , 'DESDX1' , 'DXREL2' , 'DESDX2' , 'DXREL3' , 'DESDX3' , 'REMIT' , 'ESPECI' ,
      'MOTREM' , 'FPROXCIT' , 'PROFCIT' , 'PROFNOM' , 'MEDHC')";
$sth = $conn->prepare($cabecera);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
$x=1;
$y=1;
foreach($result as $row) {
        $activeSheet->setCellValueByColumnAndRow($x,$y,$row['DESCCAMPO']);
        $x++;
}

$consulta="exec spV_HCA_Pivot '$fechaini','$fechafin','EVOPRECO','$idsede','[ACOMP], [PARENT] , [DIR] , [TEL] , [INSTITU] ,
 [TER] , [DTSCONSULTA] , [MOTCONSULTA] , [ENFACTUAL] , [HEMOCLASIF] , [EDGES] , [FEINGPRO] , [TPOCTRL] , [LEEESC] , [NROGES] ,
  [NROCONV] , [RIEBIOT] , [RBIODESC] , [HISREPT] , [PARIDAD] , [PRERIES] , [PTOSRBIO] , [CONDASOC] , [CONASO] , [PTOSCASO] ,
   [EMBACTT] , [EMBACT] , [PTOSEACT] , [RIEPSIC] , [TEMO] , [HUMO] , [SINNEU] , [PTOSPSIC] , [SOPFAMT] , [SOPFAM1] , [SOPFAM2] , 
   [TIME] , [ESPA] , [DINE] , [PTOSSFAM] , [RIEPSICPER] , [PTOSTOT] , [GESACTT] , [GESACTST] , [GESACT] , [FEULMES] ,
    [FEPROPAR] , [EGCONFUM] , [EGCONECO] , [HEMOC] , [GRUSAN] , [RH] , [SESIBGS] , [FEGRUSAN] , [REPLAB] , [HEMOG] , 
    [RESHEMO] , [FERES] , [HEMAT] , [RESHEMA] , [FRESHEMA] , [FROTIS] , [RESFROT] , [FRESFROT] , [CITOQUIM] , [RESORI] ,
     [FRESCITO] , [UROCULT] , [RESURO] , [FRESURO] , [ECOOBS] , [RESECO] , [FRESECO] , [TOXO] , [RESTOX] , [FRESTOX] ,
      [SESIBIL] , [GLICEM] , [RESGLI] , [FRESGLI] , [TESTOSULL] , [RESTEST] , [FRESTEST] , [CVATOLGLU] , [RESCVAGLU] , 
      [FRESCUGLU] , [VDRLI] , [RESVDRL] , [FRESVDRL] , [SERO] , [SERODIL] , [SIFILIS] , [CLSSIF] , [ANTSIF] , [TTOSIFI] , 
      [TTOSI] , [TTONO] , [FEAPTTO] , [DOSIS1] , [DOSIS2] , [DOSIS3] , [FETTOPA] , [DOSISP1] , [DOSISP2] , [DOSISP3] ,
       [OBSSIF] , [RETSIF] , [DOSISRT1] , [DOSISRT2] , [DOSISRT3] , [RETSIFP] , [DOSISRP1] , [DOSISRP2] , [DOSISRP3] , 
       [ELISA] , [FEELISA] , [RESELISA] , [ELISA2] , [FEELISA2] , [RESELISA2] , [CARGAV] , [CARGAVFE] , [WESTERN] , 
       [WESFEC] , [AGSHB] , [FECAGSH] , [RESAGSHB] , [TORCH] , [FETORCH] , [CITO] , [RESCITO] , [FERECIT] , [COLPOS] ,
        [RESCOL] , [FERECOL] , [CONIZ] , [FECONIZ] , [CAUT] , [FECAUT] , [HIPEMB] , [CONSEPREP] , [FECNJPRE] , [CONSEPOSP] , [FECNJPOS] , [VARTRAZ] ,
         [SIGVIT] , [FECHASV] , [NOCRTL] , [EDGEST] , [PESO] , [TALLA] , [IMC] , [PA] , [PAD] , [AU] , [PRE] , [FCF] ,
          [MOVFET] , [BACT] , [EXAMAMA] , [PZONAPTO] , [INTERV] , [CRA] , [DESCRA] , [OJOS] , [DESOJOS] , [NARIZ] ,
           [DESNARIZ] , [BOCA] , [DESBOCA] , [OIDOS] , [DESOIDOS] , [CYG] , [DESCYG] , [TYM] , [DESTYM] , [ABD] ,
            [DESABD1] , [GENUR] , [DESGENUR] , [ESPEC] , [DESESPEC] , [NEUR] , [DESNEUR] , [GLIN] , [DESGLIN] , [CPU] , 
            [DESCPU] , [EGEN] , [DESEGEN] , [EXT] , [DESEXT] , [PYFA] , [DESPYFA] , [EDEM] , [UBQEDEM] , [EDEMG] , 
            [CLAEMB] , [RIE1ERT] , [RIE4505] , [GEST] , [SINRES] , [OBDES] , [VICMAL] , [VVIOL] , [ETS] , [EMENT] ,
             [CAN] , [CANSEN] , [SIFGES] , [HIPIND] , [EDUCA] , [SEGUIM] , [VACUNAS] , [TDTT1] , [FECHATT1] , [TDTT2] , 
             [FECHATT2] , [INFLUEN] , [FECHAINF] , [DPTA] , [FECHADPT] , [OBSERVACI] , [PLNINT] , [MEDORD] , [HIERRO] ,
              [CALCIO] , [ACIDO] , [EXAM] , [OTTTO] , [SIGNOS] , [OBSER] , [DXPPAL] , [DESDXP] , [DXREL1] , [DESDX1] , 
              [DXREL2] , [DESDX2] , [DXREL3] , [DESDX3] , [REMIT] , [ESPECI] , [MOTREM] , [FPROXCIT] , [PROFCIT] ,
               [PROFNOM] , [MEDHC]' ";

$sth = $conn->prepare($consulta);
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
$x=1;
$y=2;
foreach($result as $key=>$row) {
    foreach($row as $key2=>$row2){
        //$activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace(array('/\s\s+/','/--/','/++/','/==/'), ' ', $row2));
        $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace($caracteres, ' ', $row2));
        // $activeSheet->setCellValueByColumnAndRow($x,$y,preg_replace(array('/\s\s+/','/--/','/++/','/==/'), ' ', $row2));
        $x++;
    }
    $y++;
    $x=1;
}

$filename='prenatal_evolucion_';
//$activeSheet->setCellValue('A1' , 'New file content')->getStyle('A1')->getFont()->setBold(true);
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); /*-- $filename is  xsl filename xlsx---*/
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
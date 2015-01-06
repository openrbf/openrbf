<?php 
require_once('fpdf.php');
//include('/pbf_app/libraries/numbers/numbers_words.php');
include(FCPATH.'/pbf_app/libraries/numbers/numbers_words.php');

class FPDF_LIB extends FPDF
{
var $widths;
var $aligns;
// En-tête
function Header()
{
   global $quarter,$titre;
   

    // Arial gras 12
   /* $this->SetFont('Arial','',9);
    $this->Cell(100,10,utf8_decode('PORTAL FBR'),0,0,'L');
	$this->Ln();
	$this->Image( FCPATH.'cside/images/portal/pbf_portal_logo.png', 15, 20);
	$this->Ln(15);
	$this->Cell(100,10,utf8_decode('MINISTERE DE LA SANTE'),0,0,'L');
	$this->Ln();*/
	
	$this->SetFont('Arial','B',9);
	$this->Cell(100,10,utf8_decode('REPUBLIQUE DU BENIN'),0,0,'L');
    // Saut de ligne
    $this->Ln(5);
	$this->Cell(100,10,utf8_decode('Ministère de la Santé'),0,0,'L');
    // Saut de ligne
    $this->Ln(5);	
	$this->Cell(150,10,utf8_decode('Secrétariat Général'),0,0,'L');
	$this->Ln(5);	
	$this->Cell(150,10,utf8_decode('Projet de Renforcement de la performance du Système de santé (PRPSS)'),0,0,'L');
	$this->Ln(5);	
	
	//$this->Cell(150,10,utf8_decode("parametre".$this->params['entity_geozone_id']),0,0,'L');
	
	$this->Cell(150,10,utf8_decode($this->params[0]),0,0,'L');
	$this->SetFont('Arial','B',9);
	$this->Ln(10);
	$this->SetFillColor(192,192,192);	
	$this->Cell(38,10,utf8_decode('Zone sanitaire de : '),0,0,'L',true);
	$this->SetFont('Arial','',9);
	$this->Cell(($this->params[0]=='Facture trimestrielle par indicateur et par zone')?90:180,10,utf8_decode($this->params[1]),0,0,'L',true);
	$this->SetFont('Arial','B',9);
	$this->Cell(18,10,utf8_decode('Période : '),0,0,'L',true);
	$this->SetFont('Arial','',9);
	$this->Cell(42,10,"Trimestre ".$this->params[2]."    ".$this->params[3],0,0,'L',true);	
	$this->Ln();
	
}

// Pied de page
function Footer()
{
   // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Arial italique 8
    $this->SetFont('Arial','I',8);
    // Couleur du texte en gris
   // $this->SetTextColor(128);
    // Numéro de page
	$this->Cell(20,10,date("Y-m-d h:i:s"),0,0,'L');
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'R');
}
function SetCol($col)
{
    // Positionnement sur une colonne
    $this->col = $col;
    $x = 10+$col*65;
    $this->SetLeftMargin($x);
    $this->SetX($x);
}

function SetWidths($w)
{
    //Tableau des largeurs de colonnes
    $this->widths=$w;
}

function SetAligns($a)
{
    //Tableau des alignements de colonnes
    $this->aligns=$a;
}

function getMax_height($data){
	
for($i=0;$i<count($data);$i++){
		
	return max($data);
	}
}

function Row($data,$fill=false)
{
	//$this->SetFont('Arial','',6);
    //Calcule la hauteur de la ligne
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		
    $h=(5*$nb);
	
    //Effectue un saut de page si nécessaire
    $this->CheckPageBreak($h+4);
    //Dessine les cellules
	
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Sauve la position courante
        $x=$this->GetX();
        $y=$this->GetY();
        //Dessine le cadre
		
		
        $this->Rect($x,$y,$w,$h);
		
        //Imprime le texte
        $this->MultiCell($w,5,$data[$i],0,$a,$fill);
		//$this->MultiCell($w,5,$data[$i],0,$a,0);
        //Repositionne à droite
        $this->SetXY($x+$w,$y);
		
    }
    //Va à la ligne
	 $this->Ln($h);
}

function CheckPageBreak($h)
{
    //Si la hauteur h provoque un débordement, saut de page manuel
    if($this->GetY()+$h>$this->PageBreakTrigger){
        $this->AddPage($this->CurOrientation);
		$this->Ln(7);
		}
}

function NbLines($w,$txt)
{
    //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
			 $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

function facture_trim_indic_zon_table($header,$data,$data_control,$plafond)
{
	
    // Largeurs des colonnes
    //$w = array(8,62,30, 30,30, 30);
	$sous_tot_trait_mois1=0;$sous_tot_trait_mois2=0;$sous_tot_trait_mois3=0;$tot_trait_trim=0;$sous_tot_control_mois1=0;$sous_tot_control_mois2=0;$sous_tot_control_mois3=0;
	$w = array(8,102,20, 20,20, 20);
	$this->SetAligns(array('L','L','R','R','R','R'));
    // En-tête
	$this->SetWidths($w);
	$this->SetFont('Arial','B',7);
	$this->Cell(array_sum($w),7,'Formation Sanitaires Traitement',1,0,'L');
	$this->Ln();
	$this->Row($header);
   
    //$this->Ln();
	
    // Données
	$i=1;
	$this->SetFillColor(192,192,192);
    
	$this->SetFont('Arial','',7);
	$fill=false;
    foreach($data as $i_key => $i_val )
    {
	

		/*$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['credit_mois1']),number_format($i_val['credit_mois2']),number_format($i_val['credit_mois3']),number_format($i_val['credit_mois1']+$i_val['credit_mois2']+$i_val['credit_mois3'])),$fill);*/
		
		$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['credit_mois1']),number_format($i_val['credit_mois2']),number_format($i_val['credit_mois3']),number_format($i_val['credit_mois1']+$i_val['credit_mois2']+$i_val['credit_mois3'])),$fill);
		
		$fill=!$fill;
		$sous_tot_trait_mois1+=$i_val['credit_mois1'];
		$sous_tot_trait_mois2+=$i_val['credit_mois2'];
		$sous_tot_trait_mois3+=$i_val['credit_mois3'];
		$tot_trait_trim+=$i_val['credit_trim'];
		$i++;
    }
	
	/*echo $sous_tot_traitement;
	exit;*/
	$this->SetFont('Arial','B',7);
    // Note sous total traitement
    $this->Cell($w[0]+$w[1],5,'Total Formations Sanitaires Traitement',1,0,'L');
	$this->Cell($w[2],5,number_format($sous_tot_trait_mois1),1,0,'R');
	$this->Cell($w[3],5,number_format($sous_tot_trait_mois2),1,0,'R');
	$this->Cell($w[4],5,number_format($sous_tot_trait_mois3),1,0,'R');
	$this->Cell($w[5],5,number_format($sous_tot_trait_mois1+$sous_tot_trait_mois2+$sous_tot_trait_mois3),1,0,'R');
	//$this->Cell($w[5],5,number_format($tot_trait_trim),1,0,'R');
	$this->Ln(10);
	$this->SetFont('Arial','B',7);
	$this->Cell(array_sum($w),7,utf8_decode('Formation Sanitaires Contrôle'),1,0,'L');
	$this->Ln();	
	$this->Row($header);
	$i=1;
	$this->SetFont('Arial','',7);
    foreach($data_control as $i_key => $i_val )
    {
		
		$this->Row(array(16+$i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['credit_mois1']),number_format($i_val['credit_mois2']),number_format($i_val['credit_mois3']),number_format($i_val['$credit_mois1']+$i_val['credit_mois2']+$i_val['credit_mois3'])),$fill);
		$fill=!$fill;
		$sous_tot_control_mois1+=$i_val['credit_mois1'];
		$sous_tot_control_mois2+=$i_val['credit_mois2'];
		$sous_tot_control_mois3+=$i_val['credit_mois3'];
		$i++;
    }
	// Note sous total Control	
	$this->SetFont('Arial','B',7);
    $this->Cell($w[0]+$w[1],5,utf8_decode('Total Formations Sanitaires Contrôle pour les indicateurs paludisme'),1,0,'L');
	$this->Cell($w[2],5,number_format($sous_tot_control_mois1),1,0,'R');
	$this->Cell($w[3],5,number_format($sous_tot_control_mois2),1,0,'R');
	$this->Cell($w[4],5,number_format($sous_tot_control_mois3),1,0,'R');
	$this->Cell($w[5],5,number_format($sous_tot_control_mois1+$sous_tot_control_mois2+$sous_tot_control_mois3),1,0,'R');
	$this->Ln();
	$this->Cell($w[0]+$w[1],5,utf8_decode('Total Formations Sanitaires Contrôle pour les indicateurs FBR'),1,0,'L');
	$this->Cell($w[2],5,'na',1,0,'R');
	$this->Cell($w[3],5,'na',1,0,'R');
	$this->Cell($w[4],5,'na',1,0,'R');
	$this->Cell($w[5],5,number_format($plafond),1,0,'R');
	$this->Ln();
	$this->Cell($w[0]+$w[1],5,utf8_decode('Total Formations Sanitaires Contrôle'),1,0,'L');
	$this->Cell($w[2],5,'na',1,0,'R');
	$this->Cell($w[3],5,'na',1,0,'R');
	$this->Cell($w[4],5,'na',1,0,'R');
	$this->Cell($w[5],5,number_format($sous_tot_control_mois1+$sous_tot_control_mois2+$sous_tot_control_mois3+$plafond),1,0,'R');
	$this->Ln();
	//total global
	$this->Cell($w[0]+$w[1],5,utf8_decode('TOTAL '),1,0,'L');
	/*$this->Cell($w[2],5,number_format($sous_tot_trait_mois1+$sous_tot_control_mois1),1,0,'R');
	$this->Cell($w[3],5,number_format($sous_tot_trait_mois2+$sous_tot_control_mois2),1,0,'R');
	$this->Cell($w[4],5,number_format($sous_tot_trait_mois3+$sous_tot_control_mois3),1,0,'R');*/
	$this->Cell($w[2],5,'na',1,0,'R');
	$this->Cell($w[3],5,'na',1,0,'R');
	$this->Cell($w[4],5,'na',1,0,'R');
	$this->Cell($w[5],5,number_format($sous_tot_trait_mois1+$sous_tot_control_mois1+$sous_tot_trait_mois2+$sous_tot_control_mois2+$sous_tot_trait_mois3+$sous_tot_control_mois3+$plafond),1,0,'R');
	
	
	
	
}

/* facture trimestrielle par zone pour le groupe traitement*/
function facture_trim_indic_zon_trait_table($header,$data,$note_qual,$lang,$annee)
{
	
    // Largeurs des colonnes
    //$w = array(8,62,30, 30,30, 30);
	
	
	
	//$w = array(8,100,17, 15,30, 28,20,30,30);
	$w = array(8,100,17, 19,30, 28,20,28,28);
	$service_sante_maternel=array(1,42,3,4,5,6,7,8,9);
	$autre_service=array(10,11,12,13,14,15,16);
	$service_palu=array(47,48,49,50);
	$service_sante_hospitalier=array(17,18);
	$tot_payment_cat1=0;
	$tot_payment_cat4=0;
	$tot_enveloppe_fbr=0;
	
	$this->SetAligns(array('L','L','R','R','R','R','R','R','R'));
    // En-tête
	$this->SetWidths($w);
	$this->SetFont('Arial','B',7);
	
	
	$this->Row($header);
    $this->Cell(array_sum($w),7,'SERVICE DE SANTE MATERNELLE',1,0,'L');
    $this->Ln();
	
    // Données
	$i=1;
	$this->SetFillColor(192,192,192);
    
	$this->SetFont('Arial','',7);
	$fill=false;
    foreach($data as $i_key => $i_val )
    {
	if(in_array($i_key,$service_sante_maternel)){
		
		$note_qte=($annee>'2013')?(round($i_val['enveloppe_fbr_ajuste_qte']/$i_val['enveloppe_fbr']*100,2)):(round(($i_val['enveloppe_fbr_ajuste_qte']-($i_val['enveloppe_fbr']/2))*2/$i_val['enveloppe_fbr']*100,2));
		
		/*$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['quantite']),$i_val['prix_unitaire'],number_format($i_val['enveloppe_fbr']),number_format($i_val['enveloppe_fbr_ajuste_qte']),round($i_val['enveloppe_fbr_ajuste_qte']/$i_val['enveloppe_fbr']*100,2),'',''),$fill);*/
		
		$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['quantite']),$i_val['prix_unitaire'],number_format($i_val['enveloppe_fbr']),number_format($i_val['enveloppe_fbr_ajuste_qte']),$note_qte,number_format($i_val['cat1']),number_format($i_val['cat4'])),$fill);
		
		
		$fill=!$fill;
		$tot_payment_cat1+=$i_val['enveloppe_fbr_ajuste_qte'];
		$tot_enveloppe_fbr+=$i_val['enveloppe_fbr'];
		$i++;
	}
    }
	
	/*$this->SetFont('Arial','B',7);
	$this->Cell($w[0]+$w[1],5,utf8_decode('Note qualité moyenne des formations sanitaires de la zone'),1,0,'L');
	$this->Cell($w[2],5,'na',1,0,'R');
	$this->Cell($w[3],5,'na',1,0,'R');
	$this->Cell($w[4],5,'na',1,0,'R');
	$this->Cell($w[5],5,'na',1,0,'R');
	$moy=round($tot_payment_cat1/$tot_enveloppe_fbr*100,2);
	$this->Cell($w[6],5,$moy,1,0,'R');
	$this->Cell($w[7],5,'',1,0,'R');
	$this->Cell($w[8],5,'',1,0,'R');
	$this->Ln();*/
	
	$this->SetFont('Arial','B',7);
	$this->Cell(array_sum($w),7,'AUTRES SERVICES DE SANTE',1,0,'L');
	$this->Ln();
	$this->SetFont('Arial','',7);
	$fill=false;
    foreach($data as $i_key => $i_val )
    {
	if(in_array($i_key,$autre_service)){
		$note_qte=($annee>'2013')?(round($i_val['enveloppe_fbr_ajuste_qte']/$i_val['enveloppe_fbr']*100,2)):(round(($i_val['enveloppe_fbr_ajuste_qte']-($i_val['enveloppe_fbr']/2))*2/$i_val['enveloppe_fbr']*100,2));
		
		$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['quantite']),$i_val['prix_unitaire'],number_format($i_val['enveloppe_fbr']),number_format($i_val['enveloppe_fbr_ajuste_qte']),$note_qte,number_format($i_val['cat1']),number_format($i_val['cat4'])),$fill);
		$fill=!$fill;
		$tot_payment_cat1+=$i_val['enveloppe_fbr_ajuste_qte'];
		$tot_enveloppe_fbr+=$i_val['enveloppe_fbr'];
		$i++;
	}
    }
	
	
	$this->SetFont('Arial','B',7);
	$this->Cell($w[0]+$w[1],5,utf8_decode('Note qualité moyenne des formations sanitaires de la zone'),1,0,'L');
	$this->Cell($w[2],5,'na',1,0,'R');
	$this->Cell($w[3],5,'na',1,0,'R');
	$this->Cell($w[4],5,'na',1,0,'R');
	$this->Cell($w[5],5,'na',1,0,'R');
	//$moy=round($tot_payment_cat1/$tot_enveloppe_fbr*100,2);
	/*echo $tot_payment_cat1."   ".$tot_enveloppe_fbr;
	exit;*/
	$moy=($annee>'2013')?(round($tot_payment_cat1/$tot_enveloppe_fbr*100,2)):(round(($tot_payment_cat1-($tot_enveloppe_fbr/2))*2/$tot_enveloppe_fbr*100,2));
	$this->Cell($w[6],5,$moy,1,0,'R');
	$this->Cell($w[7],5,'',1,0,'R');
	$this->Cell($w[8],5,'',1,0,'R');
	$this->Ln();
	
	$this->Cell(array_sum($w),7,'SERVICE DE SANTE PALUDISME',1,0,'L');
	$this->Ln();
	$this->SetFont('Arial','',7);
	foreach($data as $i_key => $i_val )
    {
	if(in_array($i_key,$service_palu)){
		$note_qte=($annee>'2013')?(round($i_val['enveloppe_fbr_ajuste_qte']/$i_val['enveloppe_fbr']*100,2)):(round(($i_val['enveloppe_fbr_ajuste_qte']-($i_val['enveloppe_fbr']/2))*2/$i_val['enveloppe_fbr']*100,2));
		
		$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['quantite']),$i_val['prix_unitaire'],number_format($i_val['enveloppe_fbr']),number_format($i_val['enveloppe_fbr_ajuste_qte']),$note_qte,number_format($i_val['cat1']),number_format($i_val['cat4'])),$fill);
		$fill=!$fill;
		$tot_payment_cat4+=$i_val['enveloppe_fbr_ajuste_qte'];
		$tot_enveloppe_fbr+=$i_val['enveloppe_fbr'];
		$i++;
	}
    }
	$this->SetFont('Arial','B',7);
	$this->Cell(array_sum($w),7,'SERVICE DE SANTE HOSPITALIER',1,0,'L');
	$this->Ln();
	$this->SetFont('Arial','',7);
	foreach($data as $i_key => $i_val )
    {
	if(in_array($i_key,$service_sante_hospitalier)){
		
		$note_qte=($annee>'2013')?(round($i_val['enveloppe_fbr_ajuste_qte']/$i_val['enveloppe_fbr']*100,2)):(round(($i_val['enveloppe_fbr_ajuste_qte']-($i_val['enveloppe_fbr']/2))*2/$i_val['enveloppe_fbr']*100,2));
		
		$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['quantite']),$i_val['prix_unitaire'],number_format($i_val['enveloppe_fbr']),number_format($i_val['enveloppe_fbr_ajuste_qte']),$note_qte,number_format($i_val['cat1']),number_format($i_val['cat4'])),$fill);
		$fill=!$fill;
		$tot_payment_cat1+=$i_val['enveloppe_fbr_ajuste_qte'];
		$tot_enveloppe_fbr+=$i_val['enveloppe_fbr'];
		$i++;
	}
    }
	
	
	
	$this->SetFont('Arial','B',7);
	$this->Cell($w[0]+$w[1],5,utf8_decode('Total Formations Sanitaires Traitement'),1,0,'L');
	$this->Cell($w[2],5,'na',1,0,'R');
	$this->Cell($w[3],5,'na',1,0,'R');
	$this->Cell($w[4],5,'na',1,0,'R');
	$this->Cell($w[5],5,number_format($tot_payment_cat1),1,0,'R');
	$this->Cell($w[6],5,'na',1,0,'R');
	$this->Cell($w[7],5,number_format($tot_payment_cat1),1,0,'R');
	$this->Cell($w[8],5,number_format($tot_payment_cat4),1,0,'R');
    $this->Ln(10);
    $this->SetFont('Arial','',7);
    $this->Cell(35,5,utf8_decode('Sous-total à payer Catégorie 1: '),0,0,'L');
	$this->SetFont('Arial','B',7); 
	//$number=new Numbers_words();
	$number=new Numbers_words();
	
    $this->Cell(120,5,'FCFA '.number_format($tot_payment_cat1).' '.'('. $number->toWords($tot_payment_cat1,$lang).')',0,0,'L'); 
	$this->Ln();
    $this->SetFont('Arial','',7);
    $this->Cell(35,5,utf8_decode('Sous-total à payer Catégorie 4: '),0,0,'L');
	$this->SetFont('Arial','B',7); 
	$this->Cell(120,5,'FCFA '.number_format($tot_payment_cat4).' '.'('. $number->toWords($tot_payment_cat4,$lang).')',0,0,'L');
	$this->Ln();
    $this->SetFont('Arial','',7);
    $this->Cell(35,7,utf8_decode('Montant total à payer: '),0,0,'L');
	$this->SetFont('Arial','B',7); 
	$this->Cell(120,7,'FCFA '.number_format($tot_payment_cat1+$tot_payment_cat4).' '.'('. $number->toWords($tot_payment_cat1+$tot_payment_cat4,$lang).')',0,0,'L');
	$this->Ln();
    $this->SetFont('Arial','',7);
    $this->Cell(35,10,utf8_decode('Certifié par la firme chargée de la vérification (Consortium AEDES - Scen Afrik) '),0,0,'L');
	
    
	
	
}

/* facture trimestrielle par zone pour le groupe controle*/
function facture_trim_indic_zon_controle_table($header,$data,$lang,$linehf_tot)
{
	
    
	//$w = array(8,52,10, 15,20, 20,15,14,18,18);
	$w = array(8,100,17, 15,30, 28,20,20,20,20);
	$service_sante_maternel=array(1,42,3,4,5,6,7,8,9);
	$autre_service=array(10,11,12,13,14,15,16);
	$service_palu=array(47,48,49,50);
	$service_sante_hospitalier=array(17,18);
	$tot_payment_cat1=0;
	$tot_payment_cat4=0;
	$tot_produit=0;
	$tot_versee=0;
	$tot_sousplafond=0;
	$tot_dessusplafond=0;
	
	$this->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
    // En-tête
	$this->SetWidths($w);
	$this->SetFont('Arial','B',7);
	$this->Row($header);
	$this->Cell(array_sum($w),7,'SERVICE DE SANTE MATERNELLE',1,0,'L');
	$this->Ln();
	
   
    
	
    // Données
	$i=1;
	$this->SetFillColor(192,192,192);
    
	$this->SetFont('Arial','',7);
	$fill=false;
    foreach($data as $i_key => $i_val )
    {
	if(in_array($i_key,$service_sante_maternel)){
		$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['quantite']),$i_val['prix_unitaire'],number_format($i_val['enveloppe_fbr']),number_format($i_val['enveloppe_fbr_versee']),number_format($i_val['enveloppe_fbr_sousPlafond']),number_format($i_val['enveloppe_fbr_dessusPlafond']),number_format($i_val['cat1']),number_format($i_val['cat4'])),$fill);
		$fill=!$fill;
		$tot_payment_cat1+=$i_val['enveloppe_fbr_versee'];
		$tot_produit+=$i_val['enveloppe_fbr'];
		$tot_sousplafond+=$i_val['enveloppe_fbr_sousPlafond'];
		$tot_dessusplafond+=$i_val['enveloppe_fbr_dessusPlafond'];
		$i++;
	}
    }
	
	$this->SetFont('Arial','B',7);
	$this->Cell(array_sum($w),7,'AUTRES SERVICES DE SANTE',1,0,'L');
	$this->Ln();
	$this->SetFont('Arial','',7);
	$fill=false;
    foreach($data as $i_key => $i_val )
    {
	if(in_array($i_key,$autre_service)){
		$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['quantite']),$i_val['prix_unitaire'],number_format($i_val['enveloppe_fbr']),number_format($i_val['enveloppe_fbr_versee']),number_format($i_val['enveloppe_fbr_sousPlafond']),number_format($i_val['enveloppe_fbr_dessusPlafond']),number_format($i_val['cat1']),number_format($i_val['cat4'])),$fill);
		$fill=!$fill;
		$tot_payment_cat1+=$i_val['enveloppe_fbr_versee'];
		$tot_produit+=$i_val['enveloppe_fbr'];
		$tot_sousplafond+=$i_val['enveloppe_fbr_sousPlafond'];
		$tot_dessusplafond+=$i_val['enveloppe_fbr_dessusPlafond'];
		$i++;
	}
    }
	
		
	$this->SetFont('Arial','B',7);
	$this->Cell($w[0]+$w[1],5,utf8_decode('Total des crédits FS contrôle'),1,0,'L');
	$this->Cell($w[2],5,'na',1,0,'R');
	$this->Cell($w[3],5,'na',1,0,'R');
	$this->Cell($w[4],5,number_format($tot_produit),1,0,'R');
	$this->Cell($w[5],5,number_format($tot_payment_cat1),1,0,'R');
	$this->Cell($w[6],5,number_format($tot_sousplafond),1,0,'R');
	$this->Cell($w[7],5,number_format($tot_dessusplafond),1,0,'R');
	$this->Cell($w[8],5,'',1,0,'R');
	$this->Cell($w[9],5,'',1,0,'R');
	$this->Ln();
	$this->Cell(array_sum($w),7,'SERVICE DE SANTE PALUDISME',1,0,'L');
	$this->Ln();
	$this->SetFont('Arial','',7);
	foreach($data as $i_key => $i_val )
    {
	if(in_array($i_key,$service_palu)){
		$this->Row(array($i.". ",utf8_decode($i_val['indicator_title']),number_format($i_val['quantite']),$i_val['prix_unitaire'],number_format($i_val['enveloppe_fbr']),number_format($i_val['enveloppe_fbr_versee']),'na','na',number_format($i_val['cat1']),number_format($i_val['cat4'])),$fill);
		$fill=!$fill;
		$tot_payment_cat4+=$i_val['enveloppe_fbr_versee'];
		$i++;
	}
    }
	
	$this->SetFont('Arial','B',7);
	$this->Cell($w[0]+$w[1],5,utf8_decode('Total Formations Sanitaires Contrôle'),1,0,'L');
	$this->Cell($w[2],5,'na',1,0,'R');
	$this->Cell($w[3],5,'na',1,0,'R');
	$this->Cell($w[4],5,'na',1,0,'R');
	$this->Cell($w[5],5,number_format($tot_payment_cat1+$tot_payment_cat4),1,0,'R');
	$this->Cell($w[6],5,'na',1,0,'R');
	$this->Cell($w[7],5,'na',1,0,'R');
	$this->Cell($w[8],5,number_format($tot_payment_cat1),1,0,'R');
	$this->Cell($w[9],5,number_format($tot_payment_cat4),1,0,'R');	
	
	
    $this->Ln(10);
    $this->SetFont('Arial','',7);
    $this->Cell(35,5,utf8_decode('Sous-total à payer Catégorie 1: '),0,0,'L');
	$this->SetFont('Arial','B',7); 
	
	$n=new Numbers_words();
	
	 
   $this->Cell(120,5,'FCFA '.number_format($linehf_tot).' '.'('. $n->toWords($linehf_tot,$lang).')',0,0,'L');
	$this->Ln();
    $this->SetFont('Arial','',7);
    $this->Cell(35,5,utf8_decode('Sous-total à payer Catégorie 4: '),0,0,'L');
	$this->SetFont('Arial','B',7); 
	$this->Cell(120,5,'FCFA '.number_format($tot_payment_cat4).' '.'('. $n->toWords($tot_payment_cat4,$lang).')',0,0,'L');
	$this->Ln();
    $this->SetFont('Arial','',7);
    $this->Cell(35,7,utf8_decode('Montant total à payer: '),0,0,'L');
	$this->SetFont('Arial','B',7); 
	
	$this->Cell(120,7,'FCFA '.number_format($linehf_tot).' '.'('. $n->toWords($linehf_tot,$lang).')',0,0,'L');
	
	$this->Ln();
    $this->SetFont('Arial','',7);
    $this->Cell(35,10,utf8_decode('Certifié par la firme chargée de la vérification (Consortium AEDES - Scen Afrik) '),0,0,'L');
	
    
	
	
}
}



?>
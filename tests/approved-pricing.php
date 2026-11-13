<?php
/** Dependency-free calculator regression tests. All rates are isolated fixtures, never production defaults. */
define('ABSPATH',__DIR__.'/');
function sanitize_key($v){return preg_replace('/[^a-z0-9_\-]/','',strtolower($v));}
require __DIR__.'/../app/public/wp-content/plugins/prime-cleaning-estimator/includes/class-pce-approved.php';
$s=PCE_Approved::defaults();foreach(['lease'=>400,'regular'=>200] as $service=>$rate)for($b=0;$b<=5;$b++)for($a=1;$a<=4;$a++)$s['grids'][$service]["$b:$a"]=$rate;
$s['minimum']=100;$s['suburbs']=['near'=>['name'=>'Test Near','postcode'=>'3000','zone'=>1],'far'=>['name'=>'Test Far','postcode'=>'3001','zone'=>2]];$s['extras']=array_fill_keys(array_keys(PCE_Approved::EXTRAS),20);$s['carpets']=array_fill_keys(array_keys(PCE_Approved::CARPETS),25);
$p=['service'=>'regular','bedrooms'=>'2','bathrooms'=>'1','property'=>'single','frequency'=>'once','suburb'=>'near'];$count=0;
function check($label,$actual,$expected){global $count;$count++;if($actual!==$expected){fwrite(STDERR,"FAIL $label: ".json_encode([$actual,$expected])."\n");exit(1);}}
function calc($changes=[],$settings=null){global $p,$s;return PCE_Approved::calculate(array_replace($p,$changes),$settings??$s);}
check('regular grid',calc()['total'],200.0);check('lease independent grid',calc(['service'=>'lease'])['total'],400.0);check('deep multiplier',calc(['service'=>'deep'])['total'],300.0);
check('double-storey base only',calc(['property'=>'double','extras'=>['oven'=>1]])['total'],250.0);
check('weekly discount base+surcharge, extras and travel excluded',calc(['frequency'=>'weekly','property'=>'double','extras'=>['oven'=>1],'suburb'=>'far'])['total'],245.5);
check('fortnightly same discount',calc(['frequency'=>'fortnightly'])['total'],170.0);
check('deep cannot receive recurring discount',calc(['service'=>'deep','frequency'=>'weekly'])['total'],300.0);
$q=calc(['frequency'=>'weekly','property'=>'double','extras'=>['oven'=>1],'suburb'=>'far']);check('low nearest 5',$q['low'],220.0);check('high nearest 5',$q['high'],270.0);
check('studio distinct valid',calc(['bedrooms'=>'0'])['state'],'estimate');check('5 bed 4 bath boundary',calc(['bedrooms'=>'5','bathrooms'=>'4'])['state'],'estimate');
foreach([['bedrooms'=>'6'],['bathrooms'=>'5'],['service'=>'lease','furnished'=>1],['suburb'=>'unknown']] as $input)check('custom '.json_encode($input),calc($input)['state'],'custom');
$missing=$s;$missing['grids']['regular']['2:1']='';check('blank grid custom',calc([],$missing)['state'],'custom');check('blank grid null total',calc([],$missing)['total'],null);
$missing=$s;$missing['extras']['oven']=null;check('unselected missing addon harmless',calc([],$missing)['state'],'estimate');check('selected missing addon custom',calc(['extras'=>['oven'=>1]],$missing)['state'],'custom');
$zero=$s;$zero['grids']['regular']['2:1']=0;check('approved zero remains valid',calc([],$zero)['total'],100.0);check('minimum before range',calc([],$zero)['low'],90.0);
check('carpet only no storey surcharge',calc(['service'=>'carpet','property'=>'double','carpets'=>['bedroom'=>6]])['total'],150.0);
check('carpet quantities required',calc(['service'=>'carpet'])['state'],'invalid');check('missing service rejected',calc(['service'=>'bad'])['state'],'invalid');
check('tax never added',calc()['tax_note'],'No GST is added.');check('price forged ignored',calc(['total'=>1])['total'],200.0);
$missing=$s;$missing['minimum']=null;check('minimum missing custom',calc([],$missing)['state'],'custom');
$missing=$s;$missing['travel_fee']=null;check('zone 1 no fee required',calc([],$missing)['state'],'estimate');check('zone 2 missing fee custom',calc(['suburb'=>'far'],$missing)['state'],'custom');
check('reference sequence',PCE_Approved::reference(17),'PDC-1017');echo "$count calculator checks passed.\n";
check('November 12 default minimum',PCE_Approved::defaults()['minimum'],110);
$q=calc(['service'=>'lease','extras'=>['windows'=>1]]);check('included lease windows not charged twice',$q['total'],400.0);check('included windows normalised out',$q['input']['extras']['windows'],0);
$q=calc(['service'=>'lease','extras'=>['walls'=>1]]);check('extra walls ambiguous scope custom',$q['state'],'custom');check('custom has no stale price lines',$q['lines'],[]);
$q=calc(['service'=>'carpet','property'=>'double','carpets'=>['bedroom'=>1,'living'=>2,'hallway'=>1,'stairs'=>1],'suburb'=>'far']);check('individual carpet areas and travel',$q['total'],155.0);
$s110=$s;$s110['minimum']=110;$s110['grids']['regular']['2:1']=50;$q=calc([],$s110);check('approved 110 minimum floor',$q['total'],110.0);check('approved minimum low rounding',$q['low'],100.0);check('approved minimum high rounding',$q['high'],120.0);
echo "$count total calculator checks passed.\n";

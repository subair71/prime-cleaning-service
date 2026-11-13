<?php
/** November 10 proposal calculator. Legacy options and historical estimates are untouched. */
defined('ABSPATH') || exit;
class PCE_Approved {
    const OPTION = 'pce_approved_pricing_v2';
    const SERVICES = ['lease'=>'End of Lease', 'regular'=>'Regular', 'deep'=>'Deep / Spring', 'carpet'=>'Carpet Steam Only'];
    const EXTRAS = ['oven'=>'Oven', 'fridge'=>'Fridge', 'windows'=>'Inside windows', 'balcony'=>'Balcony / patio', 'walls'=>'Wall spot cleaning'];
    const CARPETS = ['bedroom'=>'Bedrooms', 'living'=>'Lounge / living rooms', 'hallway'=>'Hallways', 'stairs'=>'Flights of stairs'];
    static function defaults() {
        return ['grids'=>['lease'=>[], 'regular'=>[]], 'extras'=>array_fill_keys(array_keys(self::EXTRAS), null), 'carpets'=>array_fill_keys(array_keys(self::CARPETS), null), 'deep_multiplier'=>1.5, 'storey_percent'=>15, 'recurring_percent'=>15, 'travel_fee'=>30, 'minimum'=>110, 'suburbs'=>[], 'phone'=>'0491 245 156', 'whatsapp'=>'61491245156', 'email'=>'info@primedomestic.com.au', 'owner_email'=>'', 'sender_email'=>'', 'abn'=>'16 938 734 164', 'reviews_url'=>'', 'response_notice'=>'We aim to reply within 2 hours during the day, and always within 24 hours.', 'discount_message'=>'Save 15% on the base clean and double-storey surcharge with weekly or fortnightly Regular cleaning. Add-ons and travel are excluded.'];
    }
    static function settings() { return array_replace_recursive(self::defaults(), (array)get_option(self::OPTION, [])); }
    static function money($v) { return $v !== null && $v !== '' && is_numeric($v) && is_finite((float)$v) && (float)$v >= 0 ? round((float)$v, 2) : null; }
    static function clean($raw) {
        $s=self::defaults();
        foreach (['lease','regular'] as $service) for($b=0;$b<=5;$b++) for($a=1;$a<=4;$a++) $s['grids'][$service]["$b:$a"]=self::money($raw['grids'][$service]["$b:$a"]??null);
        foreach(['extras','carpets'] as $group) foreach($s[$group] as $key=>$v) $s[$group][$key]=self::money($raw[$group][$key]??null);
        foreach(['deep_multiplier','storey_percent','recurring_percent','travel_fee','minimum'] as $key) $s[$key]=self::money($raw[$key]??null);
        foreach(['phone','whatsapp','abn','response_notice','discount_message'] as $key) $s[$key]=sanitize_text_field($raw[$key]??'');
        foreach(['email','owner_email','sender_email'] as $key) $s[$key]=sanitize_email($raw[$key]??'');
        $s['reviews_url']=esc_url_raw($raw['reviews_url']??'');
        // Explicit list, never inferred from postcode prefixes or distance calculations.
        $rows=is_array($raw['suburbs']??null)?$raw['suburbs']:explode("\n",$raw['suburbs']??'');
        foreach($rows as $row) {
            $parts=is_array($row)?[$row['name']??'', $row['postcode']??'', $row['zone']??'']:array_map('trim',explode('|',$row));
            if(count($parts)===3 && $parts[0]!=='' && preg_match('/^\d{4}$/',$parts[1]) && in_array((string)$parts[2],['1','2'],true)) {
                $id=sanitize_title($parts[0].'-'.$parts[1]);
                $s['suburbs'][$id]=['name'=>sanitize_text_field($parts[0]),'postcode'=>$parts[1],'zone'=>(int)$parts[2]];
            }
        }
        return $s;
    }
    static function normalise($p) {
        $o=['service'=>sanitize_key($p['service']??''),'bedrooms'=>(string)($p['bedrooms']??''),'bathrooms'=>(string)($p['bathrooms']??''),'property'=>sanitize_key($p['property']??''),'furnished'=>!empty($p['furnished']),'frequency'=>sanitize_key($p['frequency']??'once'),'suburb'=>sanitize_key($p['suburb']??''),'extras'=>[],'carpets'=>[]];
        foreach(self::EXTRAS as $k=>$label) $o['extras'][$k]=empty($p['extras'][$k])?0:1;
        foreach(self::CARPETS as $k=>$label) $o['carpets'][$k]=isset($p['carpets'][$k]) && preg_match('/^\d{1,2}$/',(string)$p['carpets'][$k]) ? (int)$p['carpets'][$k] : (isset($p['carpets'][$k]) ? -1 : 0);
        if($o['service']==='lease') $o['extras']['windows']=0; // Safe-reach inside windows already included.
        if($o['service']!=='regular') $o['frequency']='once';
        if($o['service']==='carpet') { $o['extras']=array_fill_keys(array_keys(self::EXTRAS),0); $o['furnished']=false; }
        return $o;
    }
    static function validate($p) {
        $e=[];
        foreach($p['carpets'] as $quantity) if($quantity<0 || $quantity>99) $e['carpets']='Use whole carpet quantities from 0 to 99.';
        if(!isset(self::SERVICES[$p['service']])) $e['service']='Choose a cleaning service.';
        if($p['service']!=='carpet') {
            if(!preg_match('/^[0-6]$/',$p['bedrooms'])) $e['bedrooms']='Choose studio or number of bedrooms.';
            if(!preg_match('/^[1-5]$/',$p['bathrooms'])) $e['bathrooms']='Choose the number of bathrooms.';
            if(!in_array($p['property'],['apartment','single','double'],true)) $e['property']='Choose a property type.';
        }
        if(!in_array($p['frequency'],['once','weekly','fortnightly'],true)) $e['frequency']='Choose a cleaning frequency.';
        if($p['suburb']==='') $e['suburb']='Choose your suburb, or select suburb not listed.';
        if($p['service']==='carpet' && array_sum($p['carpets'])===0) $e['carpets']='Enter at least one carpet quantity.';
        return $e;
    }
    static function calculate($input, $s=null) {
        $s=$s??self::settings(); $p=self::normalise($input); $errors=self::validate($p);
        $r=['version'=>2,'state'=>'invalid','errors'=>$errors,'service_name'=>self::SERVICES[$p['service']]??'', 'input'=>$p,'reasons'=>[],'lines'=>[], 'total'=>null,'low'=>null,'high'=>null,'tax_note'=>'No GST is added.'];
        if($errors) return $r;
        $reason=[]; $base=0; $addons=0;
        if($p['service']==='lease' && $p['extras']['walls']) $reason[]='extra_wall_scope';
        if($p['service']!=='carpet') {
            if((int)$p['bedrooms']>=6) $reason[]='bedrooms';
            if((int)$p['bathrooms']>=5) $reason[]='bathrooms';
            if($p['service']==='lease' && $p['furnished']) $reason[]='furnished';
            $grid=$p['service']==='lease'?'lease':'regular';
            $base=self::money($s['grids'][$grid][$p['bedrooms'].':'.$p['bathrooms']]??null);
            if($base===null && !$reason) $reason[]='missing_price';
            if($p['service']==='deep') { if(self::money($s['deep_multiplier'])===null) $reason[]='missing_price'; else $base=$base===null?null:$base*$s['deep_multiplier']; }
        }
        $suburb=$s['suburbs'][$p['suburb']]??null;
        if(!$suburb) $reason[]='suburb';
        $travel=$suburb && (int)$suburb['zone']===2?self::money($s['travel_fee']):0;
        if($travel===null || self::money($s['minimum'])===null) $reason[]='missing_price';
        foreach(['extras'=>self::EXTRAS,'carpets'=>self::CARPETS] as $group=>$labels) foreach($labels as $key=>$label) {
            $quantity=$p[$group][$key]; if(!$quantity) continue;
            $rate=self::money($s[$group][$key]??null);
            if($rate===null) { $reason[]='missing_price'; continue; }
            $amount=round($quantity*$rate,2); $addons+=$amount;
            $r['lines'][]=['label'=>($group==='carpets'?'Carpet — ':'').$label.($quantity>1?' × '.$quantity:''),'amount'=>$amount];
        }
        $storey=0; $discount=0;
        if($p['service']!=='carpet' && $p['property']==='double') { if(self::money($s['storey_percent'])===null) $reason[]='missing_price'; else $storey=round((float)$base*$s['storey_percent']/100,2); }
        if($p['service']==='regular' && in_array($p['frequency'],['weekly','fortnightly'],true)) { if(self::money($s['recurring_percent'])===null) $reason[]='missing_price'; else $discount=round(((float)$base+$storey)*$s['recurring_percent']/100,2); }
        $r['zone']=$suburb?(int)$suburb['zone']:null; $r['suburb_details']=$suburb;
        if($reason) { $r['state']='custom';$r['reasons']=array_values(array_unique($reason));$r['lines']=[];return $r; }
        $subtotal=round($base+$storey-$discount+$addons+$travel,2); $total=max($subtotal,(float)$s['minimum']);
        $r['state']='estimate';$r['subtotal']=$subtotal;$r['total']=$total;$r['low']=round($total*.9/5)*5;$r['high']=round($total*1.1/5)*5;
        array_unshift($r['lines'],['label'=>'Base clean','amount'=>$base],['label'=>'Double-storey surcharge','amount'=>$storey],['label'=>'Recurring discount','amount'=>-$discount]);
        $r['lines'][]=['label'=>'Travel per visit','amount'=>$travel];
        if($total>$subtotal) $r['lines'][]=['label'=>'Minimum-charge adjustment','amount'=>round($total-$subtotal,2)];
        $r['minimum']=$s['minimum']; $r['calculated_at']=gmdate('c');
        return $r;
    }
    static function reference($id) { return 'PDC-'.(1000+(int)$id); }
    static function whatsapp($reference='') {
        $number=preg_replace('/\D/','',self::settings()['whatsapp']);
        return $number ? 'https://wa.me/'.$number.($reference?'?text='.rawurlencode('Hi Prime Domestic, my reference is '.$reference.'. Here are some photos of the place.'):'') : '';
    }
}

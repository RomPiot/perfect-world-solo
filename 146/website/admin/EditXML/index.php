<?

$config = array(
                "ip" => "127.0.0.1",
                "auth" => "1", 
                "login" => "user",
                "pass" => "admin",
                "ver" => "3" //0:1.4.4, 1:1.4.5, 2:1.2.6, 3:1.4.7
                );
    define("HOME", true);
    session_start();
    function socketsend($data, $ip)
    {
        if(!@$sock=socket_create(AF_INET, SOCK_STREAM, SOL_TCP))
	{throw new Exception("Unable to bind socket"); exit();}
        socket_connect($sock,$ip,29400);
        socket_set_block($sock);
    	socket_send($sock, $data, 8192, 0);
    	socket_recv($sock, $buf, 8192, 0);
    	socket_set_nonblock($sock);
        socket_close($sock);
        return $buf;
    }
    function cuint($data)
    {
        if($data < 128)
            return strrev(pack("C", $data));
        else if($data < 16384)
            return strrev(pack("S", ($data | 0x8000)));
        else if($data < 536870912)
            return strrev(pack("I", ($data | 0xC0000000)));
        return strrev(pack("c", -32) . pack("i", $data));
    }
    function PackString($data)
    {
        $data = iconv("UTF-8", "UTF-16LE", $data);
        return cuint(strlen($data)).$data;
    }
    function PackOctet($data)
    {
        $data = pack("H*", $data);
        return cuint(strlen($data)).$data;
    }
    function poctets()
    {
	$a = func_get_args();
	$dat = null;
	foreach($a as $v)
	{
	    $oct = pack("H*", $v);
	    $dat .= cuint(strlen($oct)).$oct;
	}
	return $dat;
    }
	/*
    function upcuint($data, &$p)
    {
	$size = (hexdec(bin2hex(substr($data,$p,1))) >= 128) ? 2 : 1;
	$uncuint = (hexdec(bin2hex(substr($data,$p, $size))) >= 128) ? hexdec(bin2hex(substr($data,$p, $size)))-32768 : hexdec(bin2hex(substr($data,$p, $size)));
	$p += $size;
	return $uncuint;
    }
	*/
    function upcuint($value, &$p)
    {    
        $FirstByte = hexdec(bin2hex(substr($value, $p, 1)));
        $Size = 1;
        $T = 0;
        if ($FirstByte >= 0x80 && $FirstByte <= 0xBF) {
            $Size = 2;
            $T = 0x8000;
        } elseif ($FirstByte >= 0xC0 && $FirstByte <= 0xDF) {
            $Size = 4;
            $T = 0xC0000000;
        } elseif ($FirstByte >= 0xE0 && $FirstByte <= 0xEF) {
            $Size = 8;
            $T = 0xE000000000000000;
        }
        $Ret = hexdec(bin2hex(substr($value, $p, $Size))) - $T;
        $p += $Size;
        return $Ret;
    }	
	
    function upoctet($data, &$p)
    {
	$size = upcuint($data, $p);
	$oct = bin2hex(substr($data,$p,$size));
	$p += $size;
	return $oct;
    }
    
    function upoctets($varstr, &$p, $data)
    {
	$vars = explode("/", $varstr);
        foreach($vars as $v)        
            $role[$v] = upoctet($data,$p);
        return $role;
    }
    function upstring($data, &$p)
    {
	$size = upcuint($data, $p);
        $oct = mb_convert_encoding(substr($data,$p,$size),"UTF-8","UTF-16LE");
        $p += $size;
        return $oct;
    }
    function getroledata($roleid)
    {
	global $config;
	$data = pack("N*",-1,$roleid);
        $redy = cuint(8003).cuint(strlen($data)).$data;
	$rb = socketsend($redy, $config['ip']);
	//echo bin2hex($rb); die();
	$p=12;
	//base
    $brole=unpack("Cbversion/Nid",substr($rb, $p, 5)); $p+=5;
    $brole['name']=upstring($rb, $p);
    $brole += unpack("Nrace/Ncls/Cgender",substr($rb, $p, 9)); $p+=9;
    $brole += upoctets("custom_data/config_data", $p, $rb);
	$brole += unpack("Ncustom_stamp/Cstatus/Ndelete_time/Ncreate_time/Nlastlogin_time/Cforbid", substr($rb, $p, 18)); $p+=18;
	if($brole['forbid'] == 1)
    {
        $brole +=unpack("Cftype/Nftime/Nfcreatetime", substr($rb, $p, 9)); $p += 9;
        $brole["freason"]=upstring($rb, $p);       
    }
	$brole['help_states'] = upoctet($rb, $p);
	$brole +=unpack("Nspouse/Nuserid", substr($rb, $p, 8)); $p+=8;
	
	if($config['ver'] == 3) {
		$brole += upoctets("cross_data", $p, $rb);
		$brole += unpack("Cbase_reserved2/Cbase_reserved3/Cbase_reserved4", substr($rb, $p, 3)); $p += 3;
	} else {
		$brole +=unpack("Nbase_reserved1", substr($rb, $p, 4)); $p += 4;
	}
	
	$role['base'] = $brole;
	
	//status
	$srole=unpack("Csversion/Nlevel/Nlevel2/Nexp/Nsp/Npp/Nhp/Nmp", substr($rb, $p, 29)); $p+=29;
    $srole+= unpack("fposx", strrev(substr($rb, $p, 4)));
    $srole+= unpack("fposy", strrev(substr($rb, $p, 4*2)));
    $srole+= unpack("fposz", strrev(substr($rb, $p, 4*3))); $p+=12;
    $srole+=unpack("Nworldtag/Ninvader_state/Ninvader_time/Npariah_time/Nreputation", substr($rb, $p, 20)); $p+=20;
	$srole += upoctets("custom_status/filter_data/charactermode/instancekeylist", $p ,$rb);
	$srole+=unpack("Ndbltime_expire/Ndbltime_mode/Ndbltime_begin/Ndbltime_used/Ndbltime_max/Ntime_used", substr($rb, $p, 24)); $p+= 24;
	$srole["dbltime_data"]=upoctet($rb, $p);
    $srole+= unpack("nstoresize", substr($rb, $p, 2)); $p+=2;
	if($config['ver'] == 1)
	{
	    $srole+=upoctets("petcorral/property/var_data/skills/storehousepasswd/waypointlist/coolingtime/npc_relation/multi_exp_ctrl/storage_task/faction_contrib/force_data", $p ,$rb);
	    $srole+=unpack("Cstatus_reserved31/nstatus_reserved32/Nstatus_reserved4/Nstatus_reserved5", substr($rb, $p, 11));
	    $p+=11;
	}
	if($config['ver'] == 0)
	{
	    $srole+=upoctets("petcorral/property/var_data/skills/storehousepasswd/waypointlist/coolingtime/npc_relation/multi_exp_ctrl/storage_task/faction_contrib", $p ,$rb);
	    $srole+=unpack("Nstatus_reserved2/Nstatus_reserved3/Nstatus_reserved4", substr($rb, $p, 12));
	    $p+=12;
	}
	
	if ($config['ver'] == 3) {
		$srole+=upoctets("petcorral/property/var_data/skills/storehousepasswd/waypointlist/coolingtime/npc_relation/multi_exp_ctrl/storage_task/faction_contrib/force_data/online_award/profit_time_data/country_data", $p ,$rb);
		$srole+=unpack("Nstatus_reserved4/Nstatus_reserved5", substr($rb, $p, 8));
	    $p+=8;
	}
	
	$role['status'] = $srole;
	

	//pocket
	$prole =unpack("Nicapacity/Nitimestamp/Nimoney", substr($rb, $p, 12));
	$p +=12;
	$prole['pcount'] = upcuint($rb, $p);
    for($i =0; $i<$prole['pcount']; $i++)
    {
        $prole['item'.$i] = unpack("Nid/Npos/Ncount/Nmax_count", substr($rb, $p, 16));
        $p +=  16;
        $prole['item'.$i]['data']= upoctet($rb, $p);
        $prole['item'.$i] += unpack("Nproctype/Nexpire_data/Nguid1/Nguid2/Nmask", substr($rb, $p, 20));
        $p += 20;
    }
    $prole +=unpack("Npocket_reserved1/Npocket_reserved2", substr($rb, $p, 8));
    $p +=8;
	$role['pocket'] = $prole;
	
	
	//equip
	$erole['ecount']=upcuint($rb, $p);
    for($i =0; $i<$erole['ecount']; $i++)
    {
        $erole['itemeqp'.$i] = unpack("Nid/Npos/Ncount/Nmax_count", substr($rb, $p, 16));
        $p +=  16;
        $erole['itemeqp'.$i]['data']= upoctet($rb, $p);
        $erole['itemeqp'.$i] += unpack("Nproctype/Nexpire_data/Nguid1/Nguid2/Nmask", substr($rb, $p, 20));
        $p += 20;
    }
	$role['equipment'] = $erole;
	
	
	//storehouse items
    $strole =unpack("Nscapacity/Nsmoney", substr($rb, $p, 8));
    $p += 8;
	$strole['stcount'] = upcuint($rb, $p);
    for($i =0; $i<$strole['stcount']; $i++)
    {
        $strole['itemstr'.$i] = unpack("Nid/Npos/Ncount/Nmax_count", substr($rb, $p, 16));
        $p +=  16;
        $strole['itemstr'.$i]['data']= upoctet($rb, $p);
        $strole['itemstr'.$i] += unpack("Nproctype/Nexpire_data/Nguid1/Nguid2/Nmask", substr($rb, $p, 20));
        $p += 20;
    }
    $strole +=unpack("Csize1/Csize2", substr($rb, $p, 2));
    $p += 2;
	
	//storehouse dresses
	$drrole['dresscount'] = upcuint($rb, $p);
    for($i =0; $i<$drrole['dresscount']; $i++)
    {
        $drrole['itemdress'.$i] = unpack("Nid/Npos/Ncount/Nmax_count", substr($rb, $p, 16));
        $p +=  16;
        $drrole['itemdress'.$i]['data']= upoctet($rb, $p);
        $drrole['itemdress'.$i] += unpack("Nproctype/Nexpire_data/Nguid1/Nguid2/Nmask", substr($rb, $p, 20));
        $p += 20;
    }
	
	//storehouse materials
	$marole['materialtcount'] = upcuint($rb, $p);
    for($i =0; $i<$marole['materialtcount']; $i++)
    {
        $marole['itemmaterial'.$i] = unpack("Nid/Npos/Ncount/Nmax_count", substr($rb, $p, 16));
        $p +=  16;
        $marole['itemmaterial'.$i]['data']= upoctet($rb, $p);
        $marole['itemmaterial'.$i] += unpack("Nproctype/Nexpire_data/Nguid1/Nguid2/Nmask", substr($rb, $p, 20));
        $p += 20;
    }	
	$strole += unpack("Nstore_reserved", substr($rb, $p, 4)); $p += 4;
	$role['storehouse'] = $strole;
	$role['dresses'] = $drrole;
	$role['materials'] = $marole;
	
	//task
	$trole = upoctets("task_data/task_complete/task_finishtime", $p, $rb);
	$trole['taskcount'] = upcuint($rb, $p);
	for($i =0; $i<$trole['taskcount']; $i++)
    {
        $trole['itemtask'.$i] = unpack("Nid/Npos/Ncount/Nmax_count", substr($rb, $p, 16));
        $p +=  16;
        $trole['itemtask'.$i]['data']= upoctet($rb, $p);
        $trole['itemtask'.$i] += unpack("Nproctype/Nexpire_data/Nguid1/Nguid2/Nmask", substr($rb, $p, 20));
        $p += 20;
    }
	$role['task'] = $trole;
	//echo '\n================\n';
	//var_dump($role);
	//echo '\n================\n';
	//echo '================\n';	
	return $role;
    }
	
	//=================
	
    function getroleid($name)
    {
	global $config;
	$data = pack("N", -1).PackString($name)."\x00";
	$redy = cuint(3033).cuint(strlen($data)).$data;
	$b = socketsend($redy, $config['ip']);
	list($id) = array_values(unpack("N", substr($b, 11, 4)));
	return $id;
    }
    function putroledata($xml)
    {
	global $config;
	$XmlData = simplexml_load_string($xml);
	$children = $XmlData->children(); 
        foreach($XmlData->children() as $child)
        {
            $i = 0;
            foreach($child->children()  as $child2)
            {
                if(!$child2->children()) $role[$child2->getName()] = $child2;
                else
                {                       
                    foreach($child2->children() as $child3)
                    {
                        $role[substr($child->getName(),0,3)][$i][$child3->getName()] = $child3;
                    }
                    $i++;
                }                    
            }         
        }
	
	$data = pack("NNC",-1,$role['id'],1).
    pack("CN",$role['bversion'],$role['id']).
    PackString($role['name']).
    pack("NNC",$role['race'],$role['cls'], $role['gender']).
    poctets($role['custom_data'],$role['config_data']).
    pack("NCN*", $role['custom_stamp'], $role['status'], $role['delete_time'], $role['create_time'], $role['lastlogin_time']).
    pack("C*", 0).
    PackOctet($role['help_states']).
    pack("N*",$role['spouse'], $role['userid']);
	
	if ($config['ver'] == 3) {
		$data .= PackOctet($role['cross_data']).pack("C*", $role['base_reserved2'], $role['base_reserved3'], $role['base_reserved4']);	
	} else {
		$data .= pack("N", $role['base_reserved1']);
	}
	
    $data .= pack("CN*", $role['sversion'], $role['level'], $role['level2'], $role['exp'], $role['sp'], $role['pp'], $role['hp'], $role['mp']).
    strrev(pack("f*", $role['posx'])).strrev(pack("f*", $role['posy'])).strrev(pack("f*", $role['posz'])).
    pack("N*", $role['worldtag'], $role['invader_state'], $role['invader_time'], $role['pariah_time'], $role['reputation']).
    poctets($role['custom_status'],$role['filter_data'],$role['charactermode'],$role['instancekeylist']).
    pack("N*", $role['dbltime_expire'], $role['dbltime_mode'], $role['dbltime_begin'], $role['dbltime_used'], $role['dbltime_max'], $role['time_used']).
    PackOctet($role["dbltime_data"]).
    pack("n*", $role['storesize']);
	if($config['ver'] == 1)
	{
	    $data .= poctets($role["petcorral"],$role["property"],$role["var_data"],$role["skills"],$role["storehousepasswd"],$role["waypointlist"],
		    $role["coolingtime"],$role["npc_relation"],$role["multi_exp_ctrl"],
		    $role["storage_task"],$role["faction_contrib"],$role["force_data"]).pack("C",$role['reserved31']).
	    pack("n",$role['reserved32']).pack("N*",$role['reserved4'],$role['reserved5']);
	}
	if($config['ver'] == 0)
	{
	    $data .= poctets($role["petcorral"],$role["property"],$role["var_data"],$role["skills"],$role["storehousepasswd"],
		$role["waypointlist"],$role["coolingtime"],$role["npc_relation"],$role["multi_exp_ctrl"],$role["storage_task"],
		$role["faction_contrib"]).pack("N*",$role['reserved2'],$role['reserved3'],$role['reserved4']);
	}
	
	if ($config['ver'] == 3) {
		$data .= poctets($role["petcorral"],$role["property"],$role["var_data"],$role["skills"],$role["storehousepasswd"],$role["waypointlist"],$role["coolingtime"],$role["npc_relation"],$role["multi_exp_ctrl"],$role["storage_task"],$role["faction_contrib"],$role["force_data"],$role["online_award"],$role["profit_time_data"],$role["country_data"]).pack("N*",$role['status_reserved4'],$role['status_reserved5']);	
	}
	
    $data .= pack("NNN",$role['icapacity'],$role['itimestamp'],$role['imoney']);
	if(isset($role['poc']))
	{
	    $data.=cuint(count($role['poc']));
	    for($i = 0; $i<count($role['poc']); $i++)
	    {
		$data .=pack('N*',$role['poc'][$i]['id'],$role['poc'][$i]['pos'],$role['poc'][$i]['count'],$role['poc'][$i]['max_count']).
		PackOctet($role['poc'][$i]['data']).
		pack("N*",$role['poc'][$i]['proctype'],$role['poc'][$i]['expire_data'],$role['poc'][$i]['guid1'],$role['poc'][$i]['guid2'],$role['poc'][$i]['mask']);
	    }
	}
	else $data.= pack("C", 0);
	$data .= pack("N*",$role['pocket_reserved1'],$role['pocket_reserved2']);
	if(isset($role['equ']))
	{
	    $data.=cuint(count($role['equ']));
	    for($i = 0; $i<count($role['equ']); $i++)
	    {
		$data .=pack('N*',$role['equ'][$i]['id'],$role['equ'][$i]['pos'],$role['equ'][$i]['count'],$role['equ'][$i]['max_count']).
		PackOctet($role['equ'][$i]['data']).
		pack("N*",$role['equ'][$i]['proctype'],$role['equ'][$i]['expire_data'],$role['equ'][$i]['guid1'],$role['equ'][$i]['guid2'],$role['equ'][$i]['mask']);
	    }
	}
	else $data.= pack("C", 0);
	$data .= pack("NN", $role['scapacity'], $role['smoney']);
	if(isset($role['sto']))
	{
	    $data.=cuint(count($role['sto']));
	    for($i = 0; $i<count($role['sto']); $i++)
	    {
		$data .=pack('N*',$role['sto'][$i]['id'],$role['sto'][$i]['pos'],$role['sto'][$i]['count'],$role['sto'][$i]['max_count']).
		PackOctet($role['sto'][$i]['data']).
		pack("N*",$role['sto'][$i]['proctype'],$role['sto'][$i]['expire_data'],$role['sto'][$i]['guid1'],$role['sto'][$i]['guid2'],$role['sto'][$i]['mask']);
	    }
	}
	else $data.= pack("C", 0);
	
	if ($config['ver'] == 3) {	
		$data .= pack("C*", $role['size1'], $role['size2']);	
		if(isset($role['dre']))
		{
			$data .= cuint(count($role['dre']));
			for($i = 0; $i<count($role['dre']); $i++)
			{
				$data .=pack('N*',$role['dre'][$i]['id'],$role['dre'][$i]['pos'],$role['dre'][$i]['count'],$role['dre'][$i]['max_count']).
				PackOctet($role['dre'][$i]['data']).
				pack("N*",$role['dre'][$i]['proctype'],$role['dre'][$i]['expire_data'],$role['dre'][$i]['guid1'],$role['dre'][$i]['guid2'],$role['dre'][$i]['mask']);
			}			
		}
		else $data .= pack("C", 0);	
		if(isset($role['mat']))
		{
			$data .= cuint(count($role['mat']));
			for($i = 0; $i<count($role['mat']); $i++)
			{
				$data .=pack('N*',$role['mat'][$i]['id'],$role['mat'][$i]['mat'],$role['mat'][$i]['count'],$role['mat'][$i]['max_count']).
				PackOctet($role['mat'][$i]['data']).
				pack("N*",$role['mat'][$i]['proctype'],$role['mat'][$i]['expire_data'],$role['mat'][$i]['guid1'],$role['mat'][$i]['guid2'],$role['mat'][$i]['mask']);
			}			
		}
		else $data .= pack("C", 0);
		$data .= pack("N",$role['store_reserved']);
	} else {
		$data .= pack("N*", $role['size1'], $role['size2']);
	}
	
    $data .= poctets($role["task_data"],$role["task_complete"],$role["task_finishtime"]);
	
    if(isset($role['tas']))
	{
	    $data.=cuint(count($role['tas']));
	    for($i = 0; $i<count($role['tas']); $i++)
	    {
		$data .=pack('N*',$role['tas'][$i]['id'],$role['tas'][$i]['pos'],$role['tas'][$i]['count'],$role['tas'][$i]['max_count']).
		PackOctet($role['tas'][$i]['data']).
		pack("N*",$role['tas'][$i]['proctype'],$role['tas'][$i]['expire_data'],$role['tas'][$i]['guid1'],$role['tas'][$i]['guid2'],$role['tas'][$i]['mask']);
	    }
	}
	else $data.= pack("C", 0);
	$redy = cuint(8002).cuint(strlen($data)).$data;
	$rb = socketsend($redy, $config['ip']);
    }
    function checkAuth()
    {
	global $config;
	if($config['auth'] == 1)
	    return true;
	if($config['auth'] == 0)
	{
	    if((isset($_SESSION['login']))&& (isset($_SESSION['pass'])))
	    {
		if(($_SESSION['login'] == $config['login']) && ($_SESSION['pass'] == $config['pass']))
		    return true;
		else
		    return false;
	    }
	    else
		return false;
	}
    }
    function checkUser($login, $pass)
    {
	global $config;
	if(($login == $config["login"])&&($pass == $config["pass"]))
	{
	    $_SESSION['login'] = $login;
	    $_SESSION['pass'] = $pass;
	    header("Location:index.php");
	}
	else
	{
	    echo 'Incorrect login or password<br>';
	    echo $login." ".$pass."<br>".$config["login"]." ".$config["pass"];
	}
    }
    if(checkAuth())
    {
		if((empty($_POST['roleid'])) && (empty($_POST['rolename'])))
		{
			?>
			<form method="post">
			Input RoleID: <input type="text" name="roleid"><br>
			or Char name: <input type="text" name="rolename"><br>
			<input type="submit" value="Get XML">
			</form>
			<?
		}
		else
		{
			if($_POST['roleid']!="")
			$role = getroledata($_POST['roleid']);
			elseif($_POST['rolename']!="")	    
			$role = getroledata(getroleid($_POST['rolename']));
			else
			throw new Exception("¬ведите им€ или ID");
			$dom = new DOMDocument("1.0");
			$roled = $dom->appendChild($dom->createElement("role"));
			foreach($role as $k => $v)
			{
			$roledpart = $roled->appendChild($dom->createElement($k));
			foreach($role[$k] as $k2 => $v2)
			{
				if(!is_array($v2)){
				if(($k2!="pcount")&&($k2!="ecount")&&($k2!="stcount")&&($k2!="tcount")){
				$roledpart->appendChild($dom->createElement($k2))->appendChild($dom->createTextNode($v2));}
				}
				else
				{
				$roledparam = $roledpart->appendChild($dom->createElement('item'));
				foreach($v2 as $k3 => $v3){
					$roledparam->appendChild($dom->createElement($k3))->appendChild($dom->createTextNode($v3));}
				}
			}
			}
			$dom->formatOutput = true;
			$DataXml = $dom->saveXML();
			?>
			<form action="index.php" method="post" name="form">
			<center><textarea cols="80" rows="20" name="rolexml" style="width: 100%; word-break: break-all; background-color:#9999FF;color:black;border:1px #000 solid;margin:2px;"><?=$DataXml?></textarea></center><br>
			<input type="submit" name="save" value="Save"/>
			</form>
			<?
		}
		if(isset($_POST['rolexml']))
		{
			$text=$_POST['rolexml'];
			$text=str_replace('\"','"',$text);
			putroledata($text);
			echo "Saved";
		}
    }
    else
    {
		if((empty($_POST['login']))&& (empty($_GET['pass'])))
		{
			?>
			<form method="post">
			Login : <input name='login' type='text'><br>
			Password : <input name='pass' type='password'>
			<input type='submit' value='Enter'>
			</form>
			<?
		}
		else
		{
			checkUser($_POST['login'],$_POST['pass']);
		}
    }
echo "<center>by ColdMan</center>";

?>

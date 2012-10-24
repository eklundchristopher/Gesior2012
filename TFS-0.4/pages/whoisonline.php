<?php
if(!isset($initialized))
	exit;

if(count($config['site']['worlds']) > 1)
{
	foreach($config['site']['worlds'] as $idd => $world_n)
	{
		if($idd == (int) $_REQUEST['world'])
		{
			$world_id = $idd;
			$world_name = $world_n;
		}
	}
}
if(!isset($world_id))
{
	$world_id = 0;
	$world_name = $config['server']['serverName'];
}
if(count($config['site']['worlds']) > 1)
{
	$main_content .= '<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%><TR><TD></TD><TD>
	<FORM ACTION="" METHOD=get><INPUT TYPE="hidden" NAME="subtopic" VALUE="whoisonline"><TABLE WIDTH=100% BORDER=0 CELLSPACING=1 CELLPADDING=4><TR><TD BGCOLOR="'.$config['site']['vdarkborder'].'" CLASS=white><B>World Selection</B></TD></TR><TR><TD BGCOLOR="'.$config['site']['darkborder'].'">
	<TABLE BORDER=0 CELLPADDING=1><TR><TD>Players online on world:</TD><TD><SELECT SIZE="1" NAME="world">';
	foreach($config['site']['worlds'] as $id => $world_n)
	{
		if($id == $world_id)
			$main_content .= '<OPTION VALUE="'.htmlspecialchars($id).'" selected="selected">'.htmlspecialchars($world_n).'</OPTION>';
		else
			$main_content .= '<OPTION VALUE="'.htmlspecialchars($id).'">'.htmlspecialchars($world_n).'</OPTION>';
	}
	$main_content .= '</SELECT> </TD><TD><INPUT TYPE="image" NAME="Submit" ALT="Submit" SRC="'.$layout_name.'/images/buttons/sbutton_submit.gif">
		</TD></TR></TABLE></TABLE></FORM></TABLE>';
}
$orderby = 'name';
if(isset($_REQUEST['order']))
{
	if($_REQUEST['order']== 'level')
		$orderby = 'level';
	elseif($_REQUEST['order'] == 'vocation')
		$orderby = 'vocation';
}
$players_online_data = $SQL->query('SELECT ' . $SQL->tableName('accounts') . '.' . $SQL->fieldName('flag') . ', ' . $SQL->tableName('players') . '.' . $SQL->fieldName('name') . ', ' . $SQL->tableName('players') . '.' . $SQL->fieldName('vocation') . ', ' . $SQL->tableName('players') . '.' . $SQL->fieldName('promotion') . ', ' . $SQL->tableName('players') . '.' . $SQL->fieldName('level') . ', ' . $SQL->tableName('players') . '.' . $SQL->fieldName('skull') . ' FROM ' . $SQL->tableName('accounts') . ', ' . $SQL->tableName('players') . ' WHERE ' . $SQL->tableName('players') . '.' . $SQL->fieldName('world_id') . ' = ' . $SQL->quote($world_id) . ' AND ' . $SQL->tableName('players') . '.' . $SQL->fieldName('online') . ' = ' . $SQL->quote(1) . ' AND ' . $SQL->tableName('accounts') . '.' . $SQL->fieldName('id') . ' = ' . $SQL->tableName('players') . '.' . $SQL->fieldName('account_id') . ' ORDER BY ' . $SQL->fieldName($orderby))->fetchAll();
$number_of_players_online = 0;
$vocations_online_count = array(0,0,0,0,0); // change it if you got more then 5 vocations
$players_rows = '';
foreach($players_online_data as $player)
{
	$vocations_online_count[$player['vocation']] += 1;
	$bgcolor = (($number_of_players_online++ % 2 == 1) ?  $config['site']['darkborder'] : $config['site']['lightborder']);
	$skull = '';
	if ($player['skull'] == 4)
		$skull = "<img style='border: 0;' src='./images/skulls/redskull.gif'/>";
	else if ($player['skull'] == 5)
		$skull = "<img style='border: 0;' src='./images/skulls/blackskull.gif'/>";

	$players_rows .= '<TR BGCOLOR='.$bgcolor.'><TD WIDTH=70%><A HREF="?subtopic=characters&name='.urlencode($player['name']).'">'.htmlspecialchars($player['name']).$skull.'<img src="./images/flags/' . $player['flag'] . '.png" title="Country: ' . $player['flag'] . '" alt="' . $player['flag'] . '" /></A></TD><TD WIDTH=10%>'.$player['level'].'</TD><TD WIDTH=20%>'.htmlspecialchars($vocation_name[$player['promotion']][$player['vocation']]).'</TD><TD></TD></TR>';
}		
if($config['site']['private-servlist.com_server_id'] > 0)
{
	$main_content.= '<TABLE BORDER=0 CELLPADDING=4 CELLSPACING=1 WIDTH=100%><TR BGCOLOR="'.$config['site']['vdarkborder'].'"><TD WIDTH=10% CLASS=white><center><B>Players Online Chart</B></TD></TR></TABLE><table align="center"><tr><td><img src="http://private-servlist.com/server-chart/' . $config['site']['private-servlist.com_server_id'] . '.png" width="500px" /></td></tr></table>';
}
if($number_of_players_online == 0)
{
	//server status - server empty
	$main_content .= '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR="'.$config['site']['vdarkborder'].'"><TD CLASS=white><B>Server Status</B></TD></TR><TR BGCOLOR='.$config['site']['darkborder'].'><TD><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=1><TR><TD>Currently no one is playing on <b>'.htmlspecialchars($config['site']['worlds'][$world_id]).'</b>.</TD></TR></TABLE></TD></TR></TABLE><BR>';
}
else
{
	//server status - someone is online
	$main_content .= '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR="'.$config['site']['vdarkborder'].'"><TD CLASS=white><B>Server Status</B></TD></TR><TR BGCOLOR='.$config['site']['darkborder'].'><TD><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=1><TR><TD>Currently '.$number_of_players_online.' players are online - <b>'.$config['status']['serverStatus_players'] .' ' . (($config['status']['serverStatus_players']  > 1) ? 'are' : 'is') . ' active</b> and <b>'.($number_of_players_online-$config['status']['serverStatus_players']).' ' . (($number_of_players_online-$config['status']['serverStatus_players']  > 1) ? 'are' : 'is') . ' AFK</b> on <b>'.htmlspecialchars($config['site']['worlds'][$world_id]).'</b>.</TD></TR></TABLE></TD></TR></TABLE><BR>';


	$main_content .= '<table width="200" cellspacing="1" cellpadding="0" border="0" align="center">
		<tbody>
			<tr>
				<tr bgcolor="'.$config['site']['darkborder'].'">
				<td><img src="images/vocations/sorcerer.png" /></td>
				<td><img src="images/vocations/druid.png" /></td>
				<td><img src="images/vocations/paladin.png" /></td>
				<td><img src="images/vocations/knight.png" /></td>
			</tr>
			<tr>
				<tr bgcolor="'.$config['site']['vdarkborder'].'">
				<td style="text-align: center;"><strong style="color:white">Sorcerers</strong></td>
				<td style="text-align: center;"><strong style="color:white">Druids</strong></td>
				<td style="text-align: center;"><strong style="color:white">Paladins</strong></td>
				<td style="text-align: center;"><strong style="color:white">Knights</strong></td>
			</tr>
			<tr>
				<TR BGCOLOR="'.$config['site']['lightborder'].'">
				<td style="text-align: center;">'.$vocations_online_count[1].'</td>
				<td style="text-align: center;">'.$vocations_online_count[2].'</td>
				<td style="text-align: center;">'.$vocations_online_count[3].'</td>
				<td style="text-align: center;">'.$vocations_online_count[4].'</td>
			</tr>
		</tbody>
	</table>
	<div style="text-align: center;">&nbsp;</div>';

	//list of players
	$main_content .= '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR="'.$config['site']['vdarkborder'].'"><TD><A HREF="?subtopic=whoisonline&order=name&world='.$world_id.'" CLASS=white>Name</A></TD><TD><A HREF="?subtopic=whoisonline&order=level&world='.urlencode($world_id).'" CLASS=white>Level</A></TD><TD><A HREF="?subtopic=whoisonline&order=vocation&world='.urlencode($world_id).'" CLASS=white>Vocation</TD></TR>'.$players_rows.'</TABLE>';
	//search bar
	$main_content .= '<BR><FORM ACTION="?subtopic=characters" METHOD=post>  <TABLE WIDTH=100% BORDER=0 CELLSPACING=1 CELLPADDING=4><TR><TD BGCOLOR="'.$config['site']['vdarkborder'].'" CLASS=white><B>Search Character</B></TD></TR><TR><TD BGCOLOR="'.$config['site']['darkborder'].'"><TABLE BORDER=0 CELLPADDING=1><TR><TD>Name:</TD><TD><INPUT NAME="name" VALUE="" SIZE="29" MAXLENGTH="29"></TD><TD><INPUT TYPE="image" NAME="Submit" SRC="'.$layout_name.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD></TR></TABLE></TD></TR></TABLE></FORM>';
}
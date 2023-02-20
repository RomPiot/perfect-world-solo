<html>
<head>
<script language="JavaScript">
function recalculate()
{
var x=0;
if (document.getElementById("1").checked != false)
	{
		x=x+1;
	}
if (document.getElementById("2").checked != false)
	{
		x=x+2;
	}
if (document.getElementById("4").checked != false)
	{
		x=x+4;
	}
if (document.getElementById("8").checked != false)
	{
		x=x+8;
	}
if (document.getElementById("16").checked != false)
	{
		x=x+16;
	}
if (document.getElementById("32").checked != false)
	{
		x=x+32;
	}
if (document.getElementById("64").checked != false)
	{
		x=x+64;
	}
if (document.getElementById("128").checked != false)
	{
		x=x+128;
	}
if (document.getElementById("256").checked != false)
	{
		x=x+256;
	}
if (document.getElementById("512").checked != false)
	{
		x=x+512;
	}
if (document.getElementById("1024").checked != false)
	{
		x=x+1024;
	}
if (document.getElementById("2048").checked != false)
	{
		x=x+2048;
	}
document.getElementById("0").value = x ;
}
</script>
</head>
<body>
<div align="center">
<table width="15%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td colspan="3">
			<font size="+1" style="bold">Mask ID Generator</font><br>
		</td>

	</tr>
<form>
	<tr>
		<td>
			Blademaster:
		</td>
		<td>
			<input id="1" type="checkbox" checked="checked" onclick="recalculate()">
		</td>
		<td>

			Wizard:
		</td>
		<td>
			<input id="2" type="checkbox" checked="checked" onclick="recalculate()">	
		</td>
	<tr>
	<tr>
		<td>
			Venomancer:
		</td>
		<td>
			<input id="8" type="checkbox" checked="checked" onclick="recalculate()">
		</td>
		<td>
			Barbarian:
		</td>

		<td>
			<input id="16" type="checkbox" checked="checked" onclick="recalculate()">	
		</td>
	<tr>
	<tr>
		<td>
			Archer:
		</td>
		<td>
			<input id="64" type="checkbox" checked="checked" onclick="recalculate()">

		</td>
		<td>
			Cleric:
		</td>
		<td>
			<input id="128" type="checkbox" checked="checked" onclick="recalculate()">	
		</td>
	<tr>
	<tr>
		<td>
			Psychic:
		</td>

		<td>
			<input id="4" type="checkbox" onclick="recalculate()">
		</td>
		<td>
			Assassin:
		</td>
		<td>
			<input id="32" type="checkbox" onclick="recalculate()">	
		</td>
	<tr>
	<tr>
		<td>
			Seeker:
		</td>

		<td>
			<input id="256" type="checkbox" onclick="recalculate()">
		</td>
		<td>
			Mystic:
		</td>
		<td>
			<input id="512" type="checkbox" onclick="recalculate()">	
		</td>
	<tr>
	<tr>
		<td>
			Duskblade:
		</td>

		<td>
			<input id="1024" type="checkbox" onclick="recalculate()">
		</td>
		<td>
			Stormbringer:
		</td>
		<td>
			<input id="2048" type="checkbox" onclick="recalculate()">	
		</td>
	<tr>
	<tr>
		<td colspan="4">
			Output:<input name="MaskID" id="0" value="219" width="5"><br>
		</td>
	</tr>
</form>
</div>
</body>
</html>
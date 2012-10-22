<div id="form" style="width:100%; height:25%">
    <input id="maptype" type="hidden" value="roadmap"/>
    <table align="center" valign="center">
        <tr>
            <td colspan="7" align="center"><b>Calcola il costo del tuo taxi</b></td>
        </tr>
        <tr>
            <td colspan="7">&nbsp;</td>
        </tr>
        <tr>
            <td>Indirizzo di partenza:</td>
            <td>&nbsp;</td>
            <td><input type="text" name="address1" id="address1" size="50"/></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Indirizzo di arrivo:</td>
            <td>&nbsp;</td>
            <td><input type="text" name="address2" id="address2" size="50"/></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Mezzo:
                <select id="travelMode" name="travelMode">
                    <option value="driving">Taxi</option>
                    <option value="walking">A piedi</option>
                    <option value="bicycling">In bicicletta (Stati Uniti)</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="7">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" align="center"><input type="button" value="Mostra" onclick="initialize();"/></td>
        </tr>
    </table>
</div>
<center><div style="width:100%; height:10%" id="distance_direct"></div></center>
<center><div style="width:100%; height:10%" id="distance_road"></div></center>

<center><div id="map_canvas" style="width:600px; height:400px"></div></center>

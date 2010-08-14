<form name="reg" method="post" action="memcp.php?action=profile&amp;typeid=2" onSubmit="return validate(this)" style="zoom:1"> 
<input type="hidden" name="formhash" value="7ce31102" /> 
<link href="forumdata/cache/style_1_seditor.css?iON" rel="stylesheet" type="text/css" /> 
<h1>ä¸ªäººèµ?æ??</h1> 
<table summary="ä¸ªäººèµ?æ??" cellspacing="0" cellpadding="0" class="formtable"> 
<tr> 
<th><label for="nicknamenew">æ?µç§°</label></th> 
<td><input type="text" name="nicknamenew" id="nicknamenew" size="25" value="" class="txt" /></td> 
</tr> 
<tr> 
<th><label for="cstatusnew">è?ªå®?ä¹?å¤´è¡?</label></th> 
<td><input type="text" name="cstatusnew" id="cstatusnew" size="25" value="" class="txt" /></td> 
</tr> 
<tr> 
<th valign="top"><label for="signaturemessage">ä¸ªäººç­¾å<br /><a href="javascript:;" onclick="allowbbcode = allowsigbbcode;allowimgcode = allowsigimgcode;$('signaturepreview').innerHTML = bbcode2html($('signaturemessage').value)">é¢?è§?</a></label></th> 
<td valign="top"> 
<div id="signaturepreview"></div> 
<div class="editor_tb" style="width: 274px"> 
<div> 
<a href="javascript:;" title="ç²?ä½?" class="tb_bold" onclick="seditor_insertunit('signature', '[b]', '[/b]')">B</a> 
<a href="javascript:;" title="å?¾ç??" class="tb_img" onclick="seditor_insertunit('signature', '[img]', '[/img]')">Image</a><a href="javascript:;" title="æ?å?¥é?¾æ?¥" class="tb_link" onclick="seditor_insertunit('signature', '[url]', '[/url]')">Link</a> 
</div> 
</div> 
<textarea rows="8" cols="30" id="signaturemessage" name="signaturenew" class="txtarea" style="margin-top; -1px; border-top: none;"></textarea> 
<span class="left"> 
!discuzcode! å¯ç?¨<br /> 
[img] ä»£ç  å¯ç?¨</span> 
</td> 
</tr> 
 
<tr> 
<th valign="top"><label for="biomessage">è?ªæ??ä»?ç»<br /><a href="javascript:;" onclick="allowbbcode = allowbiobbcode;allowimgcode = allowbioimgcode;$('biopreview').innerHTML = bbcode2html($('biomessage').value)">é¢?è§?</a></label></th> 
<td> 
<div id="biopreview"></div> 
<div class="editor_tb" style="width: 274px"> 
<div> 
<a href="javascript:;" title="ç²?ä½?" class="tb_bold" onclick="seditor_insertunit('bio', '[b]', '[/b]')">B</a> 
<a href="javascript:;" title="å?¾ç??" class="tb_img" onclick="seditor_insertunit('bio', '[img]', '[/img]')">Image</a><a href="javascript:;" title="æ?å?¥é?¾æ?¥" class="tb_link" onclick="seditor_insertunit('bio', '[url]', '[/url]')">Link</a> 
</div> 
</div> 
<textarea rows="8" cols="30" id="biomessage" name="bionew" class="txtarea" style="margin-top; -1px; border-top: none;"></textarea> 
<span class="left"> 
!discuzcode! å¯ç?¨<br /> 
[img] ä»£ç  å¯ç?¨</span> 
</td> 
</tr> 
 
<tr> 
<th>æ?§å?«</th> 
<td> 
<select name="gendernew"> 
<option value="1" >ç?·</option> 
<option value="2" >å¥³</option> 
<option value="0" selected="selected">ä¿å¯?</option> 
</select> 
</td> 
</tr> 
 
<tr> 
<th><label for="bdaynew">ç??æ?¥</label></th> 
<td><input type="text" name="bdaynew" id="bdaynew" size="25" onclick="showcalendar(event, this)" onfocus="showcalendar(event, this);if(this.value=='0000-00-00')this.value=''" value="0000-00-00" class="txt" prompt="modifyprofile_birthday" /></td> 
</tr> 
 
<tr> 
<th><label for="locationnew">æ¥è?ª</label></th> 
<td><input type="text" name="locationnew" id="locationnew" size="25" value="" class="txt" /></td> 
</tr> 
 
 
<tr class="sep_space"><th colspan="2"></th></tr> 
 
<tr> 
<th><label for="sitenew">ä¸ªäººç½?ç«?</label></th> 
<td><input type="text" name="sitenew" id="sitenew" size="25" value="http://" class="txt" /></td> 
</tr> 
 
<tr> 
<th><label for="qqnew">QQ</label></th> 
<td><input type="text" name="qqnew" id="qqnew" size="25" value="" class="txt"  prompt="modifyprofile_qq" /></td> 
</tr> 
 
<tr> 
<th><label for="icqnew">ICQ</label></th> 
<td><input type="text" name="icqnew" id="icqnew" size="25" value="" class="txt" /></td> 
</tr> 
 
<tr> 
<th><label for="yahoonew">Yahoo</label></th> 
<td><input type="text" name="yahoonew" id="yahoonew" size="25" value="" class="txt" /></td> 
</tr> 
 
<tr> 
<th><label for="alipaynew">æ?¯ä»?å®è´¦å·</label></th> 
<td><input type="text" name="alipaynew" id="alipaynew" size="25" value="" class="txt" /></td> 
</tr> 
 
<tr> 
<th><label for="taobaonew">é?¿é??æ?ºæ?º</label></th> 
<td><input type="text" name="taobaonew" id="taobaonew" size="25" value="" class="txt" /></td> 
</tr> 
 
<tr class="sep_space"><th colspan="2"></th></tr> 
 
<tr> 
<th><label for="msnnew">MSN</label></th> 
<td> 
<input type="text" name="msnnew" id="msnnew" size="25" value="" class="txt" /> 
</td> 
</tr> 
 
<tr> 
<th><label for="imme">MSN å?¨çº¿é??</label></th> 
<td> 
æ?¨è¿?æ²¡æ??ç»?å®? MSN&nbsp;&nbsp;
<a href="http://im.live.cn/imme/index.htm" target="_blank">ä»?ä¹?æ?¯ MSN å?¨çº¿é??</a><br /> 
<a href="https://domains.live.com/members/signup.aspx?domain=discuz.org" target="_blank">æ³¨å?? MSN ID</a>&nbsp;&nbsp;
<a href="http://im.live.cn" target="_blank">ä¸?è½½ MSN</a>&nbsp;&nbsp;
<a href="http://settings.messenger.live.com/applications/websignup.aspx?returnurl=http://localhost/discuz/misc.php?action=imme_binding&amp;privacyurl=http://localhost/discuz/" target="_blank">å¯ç?¨ MSN å?¨çº¿é??</a> 
</td> 
</tr> 
 
<script type="text/javascript"> 
updatetextarea('signaturemessage', '500 å­?è??ä»¥å??\nä¸æ?¯æ?è?ªå®?ä¹? Discuz! ä»£ç ');updatetextarea('biomessage', '200 å­?è??ä»¥å??\nä¸æ?¯æ?è?ªå®?ä¹? Discuz! ä»£ç ');</script> 
 
 
 
<tr> 
<th>&nbsp;</th> 
<td><button type="submit" class="submit" name="editsubmit" id="editsubmit" value="true" prompt="modifyprofile_submit">æäº¤</button></td> 
</tr> 
</table> 
</form>
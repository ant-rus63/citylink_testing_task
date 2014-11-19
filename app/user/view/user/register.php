<form method="post" class="my_form" id="register_form">
	<table>
		<thead>
			<tr>
				<td colspan="2">Форма регистрации</td>
			</tr> 
		</thead>
		<tbody>
                  <?php for($i=0; $i<count($errorRegisterArray); $i++){   ?>
                  <tr>
                        <td colspan="2"><span class="auth_form_error"><?php echo($errorRegisterArray[$i]); ?></span></td>
                  </tr>
                  <?php } ?>
			<tr>
				<td><span class="auth_form_txt">Логин:</span></td>
				<td><input type="text" class="my_input" name="login" id="login"></td>
			</tr>
			<tr>
				<td><span class="auth_form_txt">Пароль:</span></td>
				<td><input type="password" class="my_input" name="password" id="password"></td>
			</tr>
			<tr>
				<td><span class="auth_form_txt">Город:</span></td>
				<td><input type="text" id="city" class="my_input" name="city"></td>
			</tr>
                  <tr>
                        <td><span class="auth_form_txt"><img src="<?php echo($captcha);?>"/></span></td>
                        <td><input type="text" name="captcha" value="" id="captcha" class="my_input" /></td>
                  </tr>
			<tr>
				<td colspan="2"><input type="submit" name="submit" class="my_input" /><div id="city_advice"></div></td>
			</tr>
		</tbody>
	</table>

</form>

<script type="text/javascript" src="/js/register.js" charset="UTF-8"></script>
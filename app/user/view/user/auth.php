<form method="post" class="my_form">
<table class="auth_form_table">
	<thead>
			<tr>
				<td colspan="2">Форма авторизации</td>
			</tr> 
		</thead>
	<tbody>
            <?php for($i=0; $i<count($errorLoginArray); $i++){   ?>
            <tr>
                  <td colspan="2"><span class="auth_form_error"><?php echo($errorLoginArray[$i]); ?></span></td>
            </tr>
            <?php } ?>

		<tr>
			<td><span class="auth_form_txt">Логин:</span></td>
			<td><input type="text" name="login" value="<?php if(isset($_POST['login'])) echo($_POST['login']); ?>" class="my_input" /></td>
		</tr>
		<tr>
			<td><span class="auth_form_txt">Пароль:</span></td>
			<td><input type="password" name="password" class="my_input" /></td>
		</tr>
		<?php
		if(isset($captcha)) {
		?>
		<tr>
			<td><span class="auth_form_txt"><img src="<?php echo($captcha);?>"/></span></td>
			<td><input type="text" name="captcha" value="" class="my_input" /></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="2"><input type="submit" name="submit" class="my_input" /></td>
		</tr>
            <tr>
                  <td colspan="2"><a href="/user/register">Регистрация</a> </td>
            </tr>
	</tbody>
</table>

</form>

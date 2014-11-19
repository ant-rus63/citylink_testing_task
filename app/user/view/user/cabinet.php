<div class="cabinet_container">
      <table>
            <tr>
                  <td>Имя пользователя:</td>
                  <td><?php echo($userData->UserName); ?></td>
            </tr>
            <tr>
                  <td>Новый пароль:</td>
                  <td>
                        <input type="password" id="user_new_password" name="user_new_password"/>
                        <input type="button" id="submit_new_password" name="submit_new_password" style="width:25px" value=">>"/>
                  </td>
            </tr>
            <tr>
                  <td>Город:</td>
                  <td>
                        <input type="text" id="user_new_city" name="user_new_city" value="<?php echo($userData->City); ?>"/>
                        <input type="button" id="submit_new_city" name="submit_new_city" style="width:25px" value=">>"/>
                        <div id="city_advice" style="top: 25%; left: 20%;"></div>
                  </td>
            </tr>
            <tr>
                  <td>Роль:</td>
                  <td><?php echo($userData->RoleName); ?></td>
            </tr>
      </table>
      <p class="logout_link"><a href="/user/logout">Выйти</a> </p>
</div>

<script type="text/javascript" src="/js/cabinet.js" charset="utf-8"></script>
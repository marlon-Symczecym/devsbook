<?=$render('header', ['loggedUser' => $loggedUser]);?>

<section class="container main">

<?=$render('sidebar', ['activeMenu' => 'config']);?>


    <section class="feed mt-10">


        <div class="row">
        
            <div class="column pr-5">

            <form class="config" method="POST" action="<?=$base;?>/config/<?=$loggedUser->id?>/update" enctype="multipart/form-data">

                <?php if(!empty($flash)): ?>
                    <?php if($_SESSION['message'] == 'success'):?>
                        <div id="message" class="message success"><?=$flash?></div>
                    <?php elseif($_SESSION['message'] == 'error'): ?>
                        <div id="message" class="message error"><?=$flash?></div>
                    <?php endif; ?>
                <?php endif; ?>

                <label for="avatar" class="label">
                    Avatar
                    <input class="input-text" type="file" name="avatar" />
                </label>
                <label for="cover" class="label">
                    Capa
                    <input class="input-text" type="file" name="cover" />
                </label>

                <br>
                <br>

                <label for="name" class="label">
                    Nome
                    <input placeholder="<?=$loggedUser->name?>" class="input-text" type="text" name="name" />
                </label>

                <label for="email" class="label">
                    E-mail
                    <input placeholder="<?=$loggedUser->email?>" class="input-email" type="email" name="email" />
                </label>

                <label for="birthdate" class="label">
                    Data de nascimento
                    <input placeholder="<?=date('d/m/Y', strtotime($loggedUser->birthdate))?>" class="input-text" type="text" name="birthdate" id="birthdate"/>
                </label>

                <label for="work" class="label">
                    Profissão
                    <input placeholder="<?=$loggedUser->work?>" class="input-text" type="text" name="work" />
                </label>

                <label for="city" class="label">
                    Cidade
                    <input placeholder="<?=$loggedUser->city?>" class="input-text" type="text" name="city" />
                </label>

                <br>
                <br>

                <label for="password" class="label">
                    Nova senha
                    <input placeholder="Digite nova senha" class="input-password" type="password" name="password" />
                </label>

                <label for="password_confirmation" class="label">
                    Confirmação nova senha
                    <input placeholder="Confirme nova senha" class="input-password" type="password" name="password-confirmation" />
                </label>

                <input class="button" type="submit" value="Salvar" />

            </form>

            </div>

            <div class="column side pl-5">
                <?=$render('right-side');?>
            </div>

        </div>

    </section>

</section>

<?=$render('footer');?>

<script src="https://unpkg.com/imask"></script>
<script>
    IMask(
        document.getElementById('birthdate'),
        {
            mask:"00/00/0000"
        }
    );
</script>
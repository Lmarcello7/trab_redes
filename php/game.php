<?php

/* FILTRAR OS REQUESTS */
$filter = filter_var_array($_REQUEST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$dificuldade = $filter['level'];
$inicio = $filter['min'];
$maximo = $filter['max'];

$dif = '';
$timer = '';
switch($dificuldade){
    case 'F':
        $dificuldade = 'Fácil';
        $timer = '30';
    break;
    case 'M':
        $dificuldade = 'Médio';
        $timer = '15';
    break;
    case 'D':
        $dificuldade = 'Difícil';
        $timer = '10';
    break;
}

/* GERA O NUMERO ALEATÓRIO E SALVA EM UMA SESSÃO */
session_start();
$_SESSION['numAleatorio'] = rand($inicio, $maximo);

var_dump($_SESSION['numAleatorio']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adivinhe o jogo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <input type="hidden" name="jogador" id="jogador" value="1">
    <div class="container-fluid d-flex justify-content-center">
		<div class="mt-3 col-7">
			<div class="card">
				<div class="card-header text-center d-flex">
					<div class="col-3"></div>
                    <div class="col-6 d-flex align-items-center justify-content-center">
                        <h5>Adivinhe o número</h5>
                    </div>
                    <div class="col-3 d-flex justify-content-end">
                        <a href="../option.html" class="btn bg-light rounded">Voltar</a>
                    </div>
				</div>
				<div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-3">
                            <span>DIficuldade: <b><?= $dificuldade ?></b></span>
                        </div>
                        <div class="col-3 d-flex justify-content-end">
                            <b>Jogador: <span id="player"></span></b>
                        </div>
                    </div>
                    <hr>
                    <div class="row justify-content-center mt-3" id="divTimer">
                        <div class="col-3">
                            <i class="fa fa-clock-o danger" aria-hidden="true"></i> <span class="danger">00:<span><span id="cronometro" class="danger"><?= $timer ?></span>
                        </div>
                    </div>
					<div class="row justify-content-center mt-3" id="divGame">
                        <div class="col-4">
                            <input type="number" name="guess" id="guess" minlength="1" class="form-control form-control-sm" placeholder="Digite um número">
                        </div>
                        <div class="col-1">
                            <button type="button" class="btn btn-sm btn-primary" id="btnEnviar">
                                Enviar
                            </button>
                        </div>
                    </div>
                    <div class="row mt-3 justify-content-center" id="start">
                        <div class="col-4">
                            <button type="button" class="btn btn-success w-100" id="btnStart" onclick="iniciarCronometro('<?= $timer ?>')">
                                Jogar
                            </button>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script>
        let tempoAtual = '<?= $timer ?>';
        let intervalId;
        
        $(document).ready(() => {
            $('#player').text($('#jogador').val());
            $('#divGame').hide();

            $('#btnStart').click(() => {
                $('#start').fadeOut(500);
                $('#divGame').fadeIn(500);
            });

            $('#btnEnviar').click(() => {
                pararCronometro();
                var num = $('#guess').val(),
                    player = $('#jogador').val();


                if (num > 0 || num !== '') {
                    pararCronometro();

                    $.ajax({
                        url: 'api.php',
                        type: 'POST',
                        data: { numero: num, numGerado: '<?= $_SESSION['numAleatorio'] ?>' },
                        success: function (response) {
                            if(response.resposta){
                                alert(`Jogador ${$player} ganhou, era o número ${$num}`);
                                location.reload();
                            } else {
                                atualizaPlayer($('#jogador').val());
                                iniciarCronometro('<?= $timer ?>');
                                alert('Número Errado');
                                $('#guess').val('');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Erro na solicitação ao servidor.');
                        }
                    });
                } else {
                    alert("Jogador não tentou um número!");
                }
            });
        });

        function iniciarCronometro(tempoInicial) {
            pararCronometro();

            setTimeout(() => { //deixa um pequeno delay antes de começar
                tempoAtual = tempoInicial;
                atualizarCronometro();

                intervalId = setInterval(function() {
                    tempoAtual <= 10 ? $('.danger').addClass('text-danger') : '';

                    if (tempoAtual <= 0) {
                        $('#btnEnviar').prop('disabled', true);
                        pararCronometro();
                        atualizaPlayer($('#jogador').val());
                        $('#cronometro').text('<?= $timer ?>');
                        $('.danger').removeClass('text-danger');

                        alert('Tempo esgotado! Vez do jogador '+$('#jogador').val()+'');

                        iniciarCronometro(tempoInicial);
                        $('#btnEnviar').prop('disabled', false);
                    } else {
                        tempoAtual--;
                        atualizarCronometro();
                    }
                }, 1000);
            }, 500);
        }

        function pararCronometro() {
            clearInterval(intervalId);
        }

        function atualizarCronometro() {
            tempoAtual < 10 ? $("#cronometro").text('0'+tempoAtual) : $("#cronometro").text(tempoAtual);
        }
        
        function atualizaPlayer(player)
        {   
            if(player == '1'){
                $('#jogador').val('2');
                $('#player').text('2');
            } else {
                $('#jogador').val('1');
                $('#player').text('1');
            }
        }
    </script>
</body>
</html>
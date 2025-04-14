// script.js

// Máscaras de entrada
$('#cnpj').mask('00.000.000/0000-00', {reverse: true});
$('#telefone').mask('(00) 00000-0000');
$('#telefone-perfil').mask('(00) 00000-0000');
$('#cep').mask('00000-000');
$('#cpf').mask('000.000.000-00', {reverse: true});
$('#cpf-perfil').mask('000.000.000-00', {reverse: true});

// Função de consultar CEP
function consultarCep() {
  const cep = document.getElementById('cep').value.replace(/\D/g, ''); // remove tudo que não for número

  if (cep.length === 8) { //confere se tem 8 números
      fetch(`https://viacep.com.br/ws/${cep}/json/`)
          .then(response => response.json())
          .then(data => {
              if (!data.erro) { // substitui os endereços de acordo com o que aparece na API
                  document.getElementById('endereco').value = data.logradouro; // rua
                  document.getElementById('bairro').value = data.bairro;
                  document.getElementById('cidade').value = data.localidade;
                  document.getElementById('estado').value = data.uf;
              } else {
                  alert('CEP não encontrado!');
              }
          })
          .catch(error => {
              console.error('Erro ao consultar o CEP:', error);
          });
  } else {
      alert('CEP inválido!');
  }
}

// Validação de senha
document.getElementById('confirma_senha').addEventListener('input', function() {
  const senhaInput = document.getElementById('senha');
  const confirmaSenhaInput = document.getElementById('confirma_senha');
  const errorMessage = document.getElementById('error-message');
  
  // Verifica se as senhas são iguais
  if (senhaInput.value.trim() !== confirmaSenhaInput.value.trim()) {
    confirmaSenhaInput.classList.add('bg-red-100', 'focus:border-red-500', 'focus:ring-red-500');
    errorMessage.classList.remove('hidden');
  } else {
    confirmaSenhaInput.classList.remove('bg-red-100', 'focus:border-red-500', 'focus:ring-red-500');
    errorMessage.classList.add('hidden');
  }
});



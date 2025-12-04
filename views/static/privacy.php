<?php 
use App\Helpers\ValidationHelper;
include __DIR__ . '/../partials/header.php'; 
include __DIR__ . '/../partials/navbar.php'; 
?>

<div class="container my-5">
    <div class="neomorph-card">
        <h1 class="mb-4">🔒 Política de Privacidade</h1>
        
        <div class="mb-4">
            <p class="text-muted">Última atualização: <?= date('d/m/Y') ?></p>
        </div>
        
        <section class="mb-4">
            <h3>1. Informações que Coletamos</h3>
            <p>Coletamos as seguintes informações quando você utiliza nosso sistema:</p>
            <ul>
                <li><strong>Dados da conta Google:</strong> Nome, email e foto de perfil</li>
                <li><strong>Dados fornecidos por você:</strong> Steam Trade Link e número de telefone</li>
                <li><strong>Dados de participação:</strong> Histórico de participações em sorteios</li>
                <li><strong>Dados técnicos:</strong> Endereço IP, navegador, data e hora de acesso</li>
            </ul>
        </section>
        
        <section class="mb-4">
            <h3>2. Como Usamos Suas Informações</h3>
            <p>Utilizamos seus dados para:</p>
            <ul>
                <li>Autenticar seu acesso ao sistema</li>
                <li>Gerenciar sua participação em sorteios</li>
                <li>Entrar em contato sobre sorteios vencidos</li>
                <li>Enviar prêmios via Steam Trade</li>
                <li>Melhorar a segurança e funcionamento do sistema</li>
                <li>Cumprir obrigações legais</li>
            </ul>
        </section>
        
        <section class="mb-4">
            <h3>3. Compartilhamento de Dados</h3>
            <p>Seus dados pessoais <strong>NÃO</strong> são vendidos ou compartilhados com terceiros, exceto:</p>
            <ul>
                <li>Quando exigido por lei</li>
                <li>Nome do vencedor é exibido publicamente após o sorteio</li>
            </ul>
        </section>
        
        <section class="mb-4">
            <h3>4. Segurança</h3>
            <p>Implementamos medidas de segurança para proteger seus dados:</p>
            <ul>
                <li>Conexões HTTPS criptografadas</li>
                <li>Proteção contra SQL Injection e XSS</li>
                <li>Tokens CSRF em todos os formulários</li>
                <li>Logs de auditoria de todas as ações</li>
                <li>Validação de uploads de arquivos</li>
            </ul>
        </section>
        
        <section class="mb-4">
            <h3>5. Seus Direitos</h3>
            <p>Você tem direito a:</p>
            <ul>
                <li>Acessar seus dados pessoais</li>
                <li>Corrigir dados incorretos</li>
                <li>Solicitar exclusão de sua conta</li>
                <li>Revogar consentimento a qualquer momento</li>
                <li>Exportar seus dados</li>
            </ul>
        </section>
        
        <section class="mb-4">
            <h3>6. Cookies</h3>
            <p>Utilizamos cookies apenas para:</p>
            <ul>
                <li>Manter sua sessão ativa</li>
                <li>Garantir segurança (tokens CSRF)</li>
            </ul>
            <p>Não utilizamos cookies de rastreamento ou publicidade.</p>
        </section>
        
        <section class="mb-4">
            <h3>7. Retenção de Dados</h3>
            <p>Mantemos seus dados enquanto sua conta estiver ativa. Dados de sorteios encerrados podem ser arquivados por até 6 meses para fins de auditoria.</p>
        </section>
        
        <section class="mb-4">
            <h3>8. Menores de Idade</h3>
            <p>Nosso serviço não é destinado a menores de 18 anos. Não coletamos intencionalmente dados de menores.</p>
        </section>
        
        <section class="mb-4">
            <h3>9. Alterações nesta Política</h3>
            <p>Podemos atualizar esta política periodicamente. Notificaremos sobre mudanças significativas através do sistema.</p>
        </section>
        
        <section class="mb-4">
            <h3>10. Contato</h3>
            <p>Para questões sobre privacidade ou exercer seus direitos, entre em contato através do email de administração do sistema.</p>
        </section>
        
        <div class="alert alert-info mt-5">
            <strong>ℹ️ Nota Importante:</strong> Este sistema NÃO processa pagamentos. Qualquer valor mencionado é apenas informativo de transações realizadas externamente.
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
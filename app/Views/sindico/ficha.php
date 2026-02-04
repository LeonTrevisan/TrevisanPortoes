<div class="ficha-sindico ficha-completa">
    <button class="btn-close" data-page="sindico" onclick="voltarParaDashboard(this)">×</button>
    <h2>Ficha Completa do Síndico</h2>
    <div class="sindico-info ficha-info">
        <p>
            <h3>Informações do Síndico</h3>
        </p>
        <p><strong>Nome:</strong> <?= htmlspecialchars($sindico['nome']) ?></p>
        <p><strong>Telefone:</strong> <?= formatarTelefone($sindico['telefone']) ?></p>
    </div>

    <div class="condominios-info info">
        <h3>Condomínios Vinculados</h3>
        <?php if (empty($condominios)): ?>
            <p>Nenhum condomínio vinculado.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($condominios as $condominio): ?>
                    <li>
                        <hr>
                        <strong>Nome:</strong> <?= htmlspecialchars($condominio['nome']) ?><br>
                        <strong>Endereço:</strong> <?= htmlspecialchars(formatarEndereco($condominio)) ?><br>
                        <strong>Telefone:</strong> <?= formatarTelefone($condominio['telefone']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
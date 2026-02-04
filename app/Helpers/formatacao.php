<?php

function formatarTelefone(string $telefone): string
{
    // remove tudo que não for número
    $telefone = preg_replace('/\D/', '', $telefone);

    if (strlen($telefone) === 11) {
        // celular
        return sprintf(
            '(%s) %s-%s',
            substr($telefone, 0, 2),
            substr($telefone, 2, 5),
            substr($telefone, 7)
        );
    }

    if (strlen($telefone) === 10) {
        // fixo
        return sprintf(
            '(%s) %s-%s',
            substr($telefone, 0, 2),
            substr($telefone, 2, 4),
            substr($telefone, 6)
        );
    }

    return $telefone; // fallback
}

function formatarEndereco(array $endereco): string
{
    $partes = [];
    if (!empty($endereco['rua'])) $partes[] = $endereco['rua'];
    if (!empty($endereco['numero'])) $partes[] = $endereco['numero'];
    if (!empty($endereco['bairro'])) $partes[] = $endereco['bairro'];
    if (!empty($endereco['cidade'])) $partes[] = $endereco['cidade'];
    if (!empty($endereco['complemento'])) $partes[] = $endereco['complemento'];

    return implode(', ', $partes);
}
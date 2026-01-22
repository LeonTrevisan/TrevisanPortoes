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
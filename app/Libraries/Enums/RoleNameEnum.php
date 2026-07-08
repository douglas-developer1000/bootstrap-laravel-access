<?php

declare(strict_types=1);

namespace App\Libraries\Enums;

enum RoleNameEnum: string
{
    case SUPER_ADMIN = 'super-admin';
    case FOR_SETTINGS = 'for-settings';
    case FOR_CUSTOMER = 'for-customer';
    case FOR_PRODUCT = 'for-product';
    case FOR_STOCK_ENTRY = 'for-stock-entry';
    case FOR_RAW_EXIT = 'for-raw-exit';
    case FOR_SALE_EXIT = 'for-sale-exit';
    case FOR_SUPPLIER = 'for-supplier';
    case FOR_DISCOUNT = 'for-discount';
    case FOR_EXCHANGE = 'for-exchange';
    case FOR_PAYMENT_CARD = 'for-payment-card';
    case FOR_LOSS_EXIT = 'for-loss-exit';
    case FOR_PERSONAL_USE_EXIT = 'for-personal-use-exit';
    case FOR_DEMONSTRATION_EXIT = 'for-demonstration-exit';

    public function summary(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Possui todas as permissões liberadas e gerencia a aplicação',
            self::FOR_SETTINGS => 'Define configurações globais',
            self::FOR_CUSTOMER => 'Gerencia clientes',
            self::FOR_PRODUCT => 'Cadastra produtos',
            self::FOR_STOCK_ENTRY => 'Cadastra entrada de estoque',
            self::FOR_RAW_EXIT => 'Saída simples de estoque',
            self::FOR_SALE_EXIT => 'Saída de estoque (venda)',
            self::FOR_SUPPLIER => 'Cadastra fornecedores',
            self::FOR_DISCOUNT => 'Gerencia descontos',
            self::FOR_EXCHANGE => 'Gerencia trocas de estoque',
            self::FOR_PAYMENT_CARD => 'Gerencia cartões de crédito',
            self::FOR_LOSS_EXIT => 'Saída de estoque (descarte)',
            self::FOR_PERSONAL_USE_EXIT => 'Saída de estoque (uso pessoal)',
            self::FOR_DEMONSTRATION_EXIT => 'Saída de estoque (demonstração)',
        };
    }

    /**
     * @return array<int, string>
     */
    public function descriptions(): array
    {
        return match ($this) {
            self::SUPER_ADMIN => [
                'Realiza todas as operações dentro da aplicação'
            ],
            self::FOR_SETTINGS => [
                'Acessar e modificar diversas configurações relacionadas à aplicação e ao usuário autenticado',
            ],
            self::FOR_CUSTOMER => [
                'Registrar, editar, remover todas as possíveis informações relacionadas a clientes',
            ],
            self::FOR_PRODUCT => [
                'Gerenciar e administrar completamente as informações do catálogo interno de produtos da aplicação',
                'Utilizar as informações cadastradas para futuras entradas e/ou saídas de estoque',
            ],
            self::FOR_STOCK_ENTRY => [
                'Realizar o cadastro de entradas de produto dentro do estoque do sistema',
            ],
            self::FOR_RAW_EXIT => [
                'Registrar o cadastro de saídas de estoque de forma mais simples',
                'Visualizar o histórico de registro dessas saídas',
            ],
            self::FOR_SALE_EXIT => [
                'Registrar o cadastro de saídas de estoque categorizadas como vendas',
                'Definir pagamentos em vendas como dinheiro ou pix',
                'Ao utilizar esse tipo de saída, administrar todas as informações relacionadas com uma venda de um produto',
            ],
            self::FOR_SUPPLIER => [
                'Cadastrar informações referentes aos fornecedores de produtos',
                'Relacionar os fornecedores ao produto registrado através das entradas de estoque',
            ],
            self::FOR_DISCOUNT => [
                'Utilizar descontos percentuais previamente configurados',
                'Cadastrar de forma customizada seus tipos de descontos',
                'Utilizar descontos brutos ou percentuais',
                'Relacionar os descontos armazenados às diversas etapas de cadastro da aplicação',
            ],
            self::FOR_EXCHANGE => [
                'Registrar ou remover saídas de estoque categorizadas como trocas',
                'Visualizar o histórico das saídas de estoque',
            ],
            self::FOR_PAYMENT_CARD => [
                'Utilizar de informações de operadoras cartões de débito ou crédito previamente configuradas',
                'Cadastrar de forma customizada novos tipos de cartões',
                'Relacionar mais formas de pagamento durante saídas de estoque, além de pix ou dinheiro',
            ],
            self::FOR_LOSS_EXIT => [
                'Registrar ou remover saídas de estoque categorizadas como descarte',
                'Visualizar o histórico das saídas de estoque',
            ],
            self::FOR_PERSONAL_USE_EXIT => [
                'Registrar ou remover saídas de estoque categorizadas como "uso pessoal"',
                'Visualizar o histórico das saídas de estoque',
            ],
            self::FOR_DEMONSTRATION_EXIT => [
                'Registrar ou remover saídas de estoque categorizadas como demonstração',
                'Visualizar o histórico das saídas de estoque',
            ],
        };
    }
}

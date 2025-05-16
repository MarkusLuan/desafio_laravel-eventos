# Desafio para entrevista de emprego com Laravel e Filament

### Informações do Projeto
* Servidor de desenvolvimento: Wamp Server
* Versão do Php: 8.3
* Frameworks utilizados:
    * Composer
    * Laravel (Já vi por cima, mas nunca mexi)
    * Filament (Descobri agora sobre ele)
* Prazo: Dia 26/05/2025 (Segunda-feira)

---

### Permissões de Usuários
#### Organizador
Responsável por criar, gerenciar e promover eventos.

- [ ] Criar e editar eventos, incluindo detalhes como data, local, descrição e capacidade
- [ ] Visualizar lista de inscritos
- [ ] Gerenciar inscrições.
- [ ] Receber pagamentos
- [ ] acompanhar o status financeiro do evento.

#### Inscrito
Usuários que buscam participar de eventos, realizando inscrições e pagamentos.
- [ ] Procurar eventos por categoria, data ou localização
- [ ] Se inscrever em eventos e realizar pagamentos
- [ ] Visualizar histórico de eventos inscritos e pagamentos efetuados.

#### Administrador
Responsável pela manutenção e supervisão geral do marketplace.
- [ ] Gerenciar contas de usuários e organizadores.
- [ ] Acessar relatórios de vendas e participação nos eventos.
- [ ] Resolver disputas e questões de suporte.

---

### Entidades
#### Eventos

* Atributos:
    * ID
    * Título
    * Descrição
    * Data
    * Localização
    * Organizador
    * Capacidade
    * Preço
* Funcionalidades:
    - [ ] Listagem
    - [ ] Busca
    - [ ] Inscrição
    - [ ] Cancelamento.

#### Inscrições
* Atributos:
    * ID
    * usuário inscrito
    * Evento
    * Data de Inscrição
    * Status
* Funcionalidades:
    - [ ] Realização de inscrição
    - [ ] Cancelamento
    - [ ] Histórico

#### Pagamentos
* Atributos:
    * ID
    * Valor
    * Método de pagamento
    * Status
    * Data
* Funcionalidades:
    - [ ] Processamento de pagamentos
    - [ ] Histórico
    - [ ] Reembolsos

#### Usuários
* Atributos:
    * ID
    * Nome
    * Email
    * Senha
    * Tipo:
        * Organizador
        * Inscrito
        * administrador
* Funcionalidades:
    - [ ] Registro
    - [ ] Autenticação
    - [ ] Edição de perfil


### Funcionalidades Adicionais
- [ ] Notificações: Enviar notificações por e-mail ou no aplicativo para lembretes de eventos, confirmações de inscrição, e atualizações importantes.
- [ ] Relatórios e Análises: Ferramentas para análise de dados de eventos, inscrições e pagamentos.
Considerações de Segurança e Privacidade
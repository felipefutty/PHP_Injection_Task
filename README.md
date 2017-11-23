## Background
Um site genérico possui uma base de usuários, permitindo que os mesmo se autentiquem no site acessando sua página pessoal, através do mecanismo clássico de login/senha. Além disso, o site permite que novos usuários se cadastrem no sistema, tendo acesso a sua página pessoal. Para controle do sistema, existe página especial de autenticação somente para os usuários cadastrados como administrador. Todas essas páginas podem ser encontradas através do link inicial do site: [Site Generico](http://localhost/Test/index.php)

O site foi realizado por uma empresa tercerizado e, após algum período da aplicação no mercado, foi constatado por clientes que suas contas foram invadidas. Dessa forma, a empresa proprietária do site contratou uma empresa para verificar o problema de vazamento.

Você, como um dos analistas de segurança da empresa contratada, foi responsabilizado por buscar falhas de ``injection`` no sistema. Para essa tarefa, foi estabelicido 3 problemas que deveriam ser replicados:

* Autenticação como qualquer usuário do sistema, dado que você possui apenas o login
* Encontrar número de usuários na base de dados
* Conseguir acesso a página de administrador do sistema


O site forneceu todo código usado pelo sistema através desse projeto github. Entretanto, eles não forneceram informação do banco de dados.

## Tarefa
Conseguir replicar os ataques citados acima usando ``injection``. Além disso, realizar um sucinto relatório de como foi realizado o ataque e estratégias possíveis para solução do problema encontradado.

## Background
Um site genérico possui uma base de usuários, permitindo que os mesmo se autentiquem no site acessando sua página pessoal, através do mecanismo clássico de login/senha. Além disso, o site permite que novos usuários se cadastrem no sistema, tendo acesso a sua página pessoal. Para controle do sistema, existe página especial de autenticação somente para os usuários cadastrados como administrador. Todas essas páginas podem ser encontradas através do link inicial do site: [Site Generico](http://143.106.73.88/sqlinjection/index.php)

O site foi realizado por uma empresa tercerizado e, após algum período da aplicação no mercado, foi constatado por clientes que suas contas foram invadidas. Dessa forma, a empresa proprietária do site contratou uma empresa para verificar o problema de vazamento.

Você, como um dos analistas de segurança da empresa contratada, foi responsabilizado por buscar falhas de ``injection`` no sistema. Para essa tarefa, foi estabelicido 3 problemas que deveriam ser replicados:

* Autenticação como qualquer usuário do sistema, dado que você possui apenas o login
* Encontrar número de usuários na base de dados
* Conseguir acesso a página de administrador do sistema


O site forneceu todo código usado pelo sistema através desse projeto github. Entretanto, eles não forneceram informação do banco de dados.

## Tarefa
Conseguir replicar os ataques citados acima usando ``injection``. Além disso, realizar um sucinto relatório de como foi realizado o ataque e estratégias possíveis para solução do problema encontradado.

## Solução
Primeiramente, o aluno deveria notar que única página que aceita ``SQL injection`` é a página de login de membro. Então, essa seria o ponto de vazamento para conseguir executar qualquer SQl no site. As soluções para cada tarefa ficariam da seguinte forma:

1. Para acessar como qualquer usuário, o usuário deveria usar a seguinte entrada no campo de ``login``:

	```
	' OR 1=1 LIMIT 1; -- 
	```

	Dessa forma, ele autentica como o primeiro usuário da base de membros. A palavra ``LIMIT`` é essencial, caso contrário a condição retornaria toda base (todos satisfazem a condição 1=1), e para o sistema conseguir logar precisa ter exatamente um único dado retornado.
Além disso, é importante usar apenas o campo ``login``, ao invés do campo de ``senha``, pois esse último é adicionado na SQL como o hash da entrada, inviabilizando a ``SQL injection``.

	Uma outra abordagem, é sabendo, por exemplo, o usuário ``felipe2`` é um login na base de dados. Então, pode bypassar a senha e logar como ele usando a seguinte entrada no campo ``login``:

	```
	felipe2'; -- 
	```


2.  Para conseguir contar o número de usuários na base de dados, poderia ser feita usando o seguinte entrada no campo ``login``:

	```
	' OR 1=1 LIMIT 1 OFFSET X; -- 
	```
	A ideia é substituir ``X`` por números afim de encontrar até quando pode-se deslocar a base e ainda conseguir se autenticar. Por exemplo, suponha que possua 250 dados, então, quando substituir ``X`` por qualquer valor menor ou igual que 249 o login ocorrerá, entretanto, qualquer valor maior ou igual que 250 o login falhará. Achar esse limite por uma busca binária, por exemplo, poderia ser uma abordagem possível.


3. Dado que a página de admnistrador não aceita ``injection``, o aluno deveria usar a estratégia de subir de privilégio um membro não admnistrador que ele saiba a senha. Como não é possível descobrir a senha, apenas bypassar essa para logar com um dado membro, o aluno deveria registrar um usuário dele no sistema e, através desse usuário, subir de privilégio. Com o usuário criado (digamos login ``cracker123``), ele deveria usar a seguinte entrada no campo ``login`` do membro:

	```
	root';  INSERT INTO adm (member_id) VALUES ((SELECT member_id FROM members WHERE login='cracker123')) ; --  
	```
	
	Importante notar que o aluno deveria descobrir a seguintes informações do sistema: a tabela com os membros do sistema é ``members``, a chave primária do membro é ``member_id``, o campo que identica o texto para logar é `` login`` e que um membro que possui acesso de administrador tem seu identificador adicionado na tabela ``adm``. Essas informações deveriam ser ou conseguidas analisando o código ou através de tentativa/erro norteadas pela inferência de possíveis nomes/estruturas usadas pelo DBA. Por exemplo, para descobrir o nomes das tabelas do sistema poderia ser usado a seguinte entrada no campo ``login``:

	```
	x' AND 1= (SELECT COUNT(*) FROM tabname); --
	```
	Onde ``tabname`` deveria ser substituido pelo nome da tabela que deseja saber se está no banco. Caso não exista, o site devolveria o erro informando que tabela não existe, caso contrário ela existe.
 	
**OBS:** Toda entrada de ``injection`` que usa comentários precisa de um espaço após o ``--``. Certifique-se sempre de que o espaço está presente.

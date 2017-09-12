Auth torna fácil a aplicação de autenticação em rotas, disponibilizando metódos para registro, login, lembrança de senha autorização por jwt


### Configuração
Por meio de funções estaticas é possível adicionar configurações no escopo da autenticação, como abaixo:

```
setManager(FiremonPHP\Manager\Manager $manager)
```
Esta função provê um meio de configurar o "gerenciador" de conexões do mongo na classe de autenticação

```
setRememberTime(int $milliseconds = 3600)
```
Quando o usuário deseja relembrar a senha, essa função pode ser útil para definir quanto tempo ele terá para usar o token de esquecimento gerado.

```
setCollectionName(string $name)
```
A classe Manager necessitará saber em que coleção ela irar salvar os dados referente a autenticação, ## nota vale lembrar que essa coleção deve ser evitada por outras funções ou metódos de seu aplicativo.

```
setExpireTime(int $milliseconds = 3600)   
```
Você pode e deve definir quanto tempo o token durará.

```
setSecuritySalt(string $securitySalt)
```
Você não poderá usar a classe, caso não defina um chave de segurânça para sua aplicação, e essa função exatamente isso, então você deve gerar uma chave e passar para esse metódo.

```
setTokenFields(array $fields)
```
Em determinado momento, desejemos pode adicionar mais informações no nosso payload, ao configurar campos disponiveis no token, ele deve levar em conta o que usuário possui como propriedades, ou sua propriedades pessoais.


### Autenticação 
Em alguns momentos, para que o usuário tenha acesso, ele precisa ter permissão para tal, e para isto foi implementada funções que podem ajuda.

```
login(string $username, $password) : User
```
```
register(string $username, $password, array $personalData = []) : User
```
```
loginWithToken(string $token) : User
```
```
edit(User $user) : User
```
```
rememberPassword(string $username) : bool / string
```
```
changePasswordByToken(string $username, string $rememberToken) : bool
// Ao inserir o token e username o usuário caso não esteja expirado, terá direito a mudança de senha.
```
```
validateJwt(string $token) : bool
// Tera um bom uso para o middleware implementado, já que a intenção é apenas saber se o token enviado é válido e se esta expirado ou não
```

### Entidade User
Ao chegar até aqui deve ter notado que algumas funções retornam ``User`` essa entitade foi criada para representar o usuário no banco de dados.

##### Getters
```
getName();
getUsername();
getRoles();
getToken();
getEmail();
getErrors();
getCreated();
getModified();
isLogged();
isActive();
```

##### Setters
```
setName(string $name);
setUsername(string $username);
setEmail(string $email);
setModified(\MongoDB\BSON\UTCDateTime $time);
setCreated(\MongoDB\BSON\UTCDateTime $time);
setLogged(bool $logged);
setActive(bool $active);
setPersonal(array $personalData);
setRoles(array $roles);
```


### Personal
Deve ter percebido o uso do setter ``setPersonal(array $data)`` essa função define uma entidade de dados pessoais com propriedades públicas, por tanto, caso você queira adicionar ao usuário dados adicionais, poderá usar essa função.

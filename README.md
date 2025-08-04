# Sistema de Gerenciamento de Usuários e Perfis

Este projeto foi desenvolvido com Laravel 10.x e tem como objetivo permitir o gerenciamento completo de usuários e seus perfis. A aplicação implementa autenticação, permissões, controle de acesso por perfis, e um painel administrativo com interface intuitiva.

---

✅ FUNCIONALIDADES

- Autenticação (registro, login, logout)
- CRUD de Usuários
- CRUD de Perfis
- Associação/desassociação de múltiplos perfis por usuário
- Validação de campos obrigatórios
- Controle de acesso com base no perfil "Administrador"
- Proteção de rotas para usuários autenticados
- Perfil "Administrador" cadastrado automaticamente via seeder

---

⚙️ REQUISITOS DO AMBIENTE

- PHP >= 8.0
- Composer
- Laravel >= 10.x
- MySQL 
- Docker 

---

🧱 ESTRUTURA COM DOCKER

Suba o container docker para rodar o mysql

docker-compose up --build -d 

---


🔐 USUÁRIO DE TESTE

Após rodar o seeder, você pode acessar com o seguinte usuário de teste:

Email: admin@example.com  
Senha: admin123

Email: usuario@example.com	
Senha: usuario123

Email: gerente@example.com	
Senha: gerente123

Esse usuário tem o perfil "Administrador" e pode gerenciar perfis e usuários.

---

🧩 TECNOLOGIAS UTILIZADAS

- Laravel 10.x
- Bootstrap 5 (admin template)
- jQuery + DataTables
- SweetAlert2
- MySQL
- Docker + Docker Compose

---

📁 ORGANIZAÇÃO DAS PASTAS

- app/Models: Modelos de Usuário e Perfil
- app/Http/Controllers: Lógicas de CRUD
- app/Helpers: Lógica de helper para permissões 
- resources/views: Interface (Blade)
- database/seeders: Seeder do perfil Administrador, Gerente e Usuário padrão


🧑‍💻 AUTOR

Desenvolvido por Flavio Candido  
GitHub: https://github.com/FlavioCandidoSilva

---

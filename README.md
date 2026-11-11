# tp8
TP8 – Développement d’une API RESTful avec Laravel (Gestion des Produits et Catégories)



# Instructions d'installation

## 1. Prérequis
- PHP 8.2 ou supérieur
- Composer
- MySQL
- Laravel
- Git

## 2. Cloner le projet

```bash
git clone https://github.com/YASSBELAB/tp8.git
cd tp8/lab8-api
```

## 3. Installer les dépendances

```bash
composer install
```

## 4. Configurer l'environnement

Créer le fichier `.env` :

```bash
cp .env.example .env
```

Configurer les informations de la base de données dans `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lab8
DB_USERNAME=root
DB_PASSWORD=
```

Créer la base de données `lab8` dans MySQL ou phpMyAdmin.

## 5. Générer la clé de l'application

```bash
php artisan key:generate
```

## 6. Exécuter les migrations et seeders

```bash
php artisan migrate --seed
```

## 7. Démarrer le serveur Laravel

```bash
php artisan serve
```

L'API sera accessible à l'adresse :

http://127.0.0.1:8000

## 8. Tester l'API

Endpoint principal :

```text
http://127.0.0.1:8000/api/products
```

Les opérations disponibles sont :

- GET : afficher les produits.
- POST : créer un produit.
- GET /{id} : afficher un produit.
- PUT /{id} : modifier un produit.
- DELETE /{id} : supprimer un produit.

Les filtres disponibles comprennent la recherche par nom, le prix minimum, le prix maximum, le tri et la pagination.

## 9. Tester avec Postman

Utiliser Postman pour envoyer des requêtes HTTP à l'API avec des données JSON.

**Repository GitHub :**  
https://github.com/YASSBELAB/tp8





Une requête Invoke-RestMethod réussie récupérant la liste des produits au format JSON depuis l'API locale.

<img width="1103" height="318" alt="invoke product" src="https://github.com/user-attachments/assets/1f32c680-4544-4bb9-99db-a9eb6f959ac1" />


Requête triant les produits par prix décroissant (page 1, tri price en mode desc), affichant le MacBook Air en tête.


<img width="1210" height="223" alt="paginationproicedesc" src="https://github.com/user-attachments/assets/2a17a927-8229-49e7-829b-063bcdc244ec" />


Requête affichant les détails d'un produit spécifique par son identifiant (/api/products/1), retournant le "Dell Laptop".


<img width="1118" height="237" alt="displaybyid" src="https://github.com/user-attachments/assets/7850f214-21eb-4a56-97c8-56a531a25ef4" />


Requête POST envoyée via l'API pour créer un nouveau produit ("New Laptop"), confirmant sa création avec succès et affichant ses détails (ID 21).


<img width="1166" height="402" alt="create product" src="https://github.com/user-attachments/assets/4c8661ce-a9b5-4a75-86f6-7d862332c475" />



Requête PUT envoyée via l'API pour mettre à jour le produit existant (ID 1), confirmant la modification avec succès ("Produit modifie") et affichant ses nouvelles données.

<img width="1085" height="307" alt="updateproduct" src="https://github.com/user-attachments/assets/2a9d59d2-2137-42d9-8a4a-585042d80cfc" />


Requête DELETE envoyée via l'API pour supprimer le produit (ID 1), affichant le message de confirmation "Product deleted successfully".

<img width="1141" height="173" alt="deleteproduct" src="https://github.com/user-attachments/assets/a8a299ed-79a0-482a-9f03-75b8dd38d7a9" />


equête de filtrage des produits par nom (/api/products?name=laptop), affichant les résultats correspondants comme le "HP Laptop"


<img width="1152" height="189" alt="filterproduct name" src="https://github.com/user-attachments/assets/63021f56-a746-4d14-855f-f839b98230f1" />


Requête de filtrage des produits par prix minimum (/api/products?price_min=100), affichant les produits dont le prix est supérieur ou égal à 100 comme le "HP Laptop".


<img width="1110" height="159" alt="Screenshot 2026-11-10 235545" src="https://github.com/user-attachments/assets/82db5cbb-86fe-40bb-ac2c-37a8a4997ef4" />



Requête de filtrage des produits par prix maximum (/api/products?price_max=500), affichant les produits dont le prix est inférieur ou égal à 500 comme la "Wireless Mouse".


<img width="1142" height="166" alt="Screenshot 2026-11-10 235643" src="https://github.com/user-attachments/assets/82e582c5-61c0-4dd4-9b71-c66112a2c52b" />


Requête de pagination pour afficher la deuxième page des produits (/api/products?page=2), affichant la liste correspondante à partir du produit "Sneakers".


<img width="1097" height="169" alt="Screenshot 2026-11-10 235710" src="https://github.com/user-attachments/assets/0cec72ba-0402-4940-81e4-f0efe621cbd1" />



Test de la requête GET sur l'API des produits via Postman, retournant une réponse HTTP 200 OK avec la liste paginée des produits au format JSON.


<img width="705" height="620" alt="Screenshot 2026-11-10 235955" src="https://github.com/user-attachments/assets/c2a19b9d-38b3-4917-9a7c-f4b67d78cde4" />


est d'une requête GET sur l'API via Postman avec des paramètres de recherche (page=1&sort=price&direction=desc), retournant une réponse 200 OK avec le tri des produits par prix décroissant (affichant le MacBook Air en premier).


<img width="811" height="684" alt="Screenshot 2026-11-11 000024" src="https://github.com/user-attachments/assets/39b9ba05-6743-46a9-8cf2-44125e33da86" />


Test d'une requête POST sur l'API via Postman pour créer un nouveau produit (données JSON envoyées dans le corps), retournant un statut 201 Created avec les détails du produit créé (ID 22).


<img width="915" height="625" alt="Screenshot 2026-11-11 000112" src="https://github.com/user-attachments/assets/0d3cb956-ba32-4ac3-a50c-2c2589eb170b" />


Test d'une requête GET sur l'API via Postman pour récupérer un produit spécifique (/api/products/3), retournant un statut 200 OK avec les détails du "MacBook Air".

<img width="709" height="643" alt="Screenshot 2026-11-11 000152" src="https://github.com/user-attachments/assets/026b8175-3dfd-4447-9634-79389029f1db" />


Test d'une requête PUT sur l'API via Postman pour mettre à jour un produit existant (/api/products/3), retournant un statut 200 OK avec les nouvelles données modifiées du produit ("Produit modifie")


<img width="790" height="673" alt="Screenshot 2026-11-11 000244" src="https://github.com/user-attachments/assets/7a34b522-8e77-4311-9bc5-96f536354665" />


Test d'une requête GET de filtrage par nom (name=laptop) sur l'API via Postman, retournant un statut 200 OK avec les résultats correspondants (comme le "HP Laptop").


<img width="698" height="623" alt="Screenshot 2026-11-11 000321" src="https://github.com/user-attachments/assets/b8dff851-f0ad-4ef6-8c70-eaf8083ae0fc" />


Test d'une requête GET de filtrage par prix minimum (price_min=100) sur l'API via Postman, retournant un statut 200 OK avec les résultats correspondants (comme le "HP Laptop")


<img width="711" height="655" alt="Screenshot 2026-11-11 000355" src="https://github.com/user-attachments/assets/926be6c5-3033-4948-a0ff-b373a49519e8" />




Test d'une requête GET de filtrage par prix maximum (price_max=500) sur l'API via Postman, retournant un statut 200 OK avec les résultats correspondants (comme le "Produit modifie")


<img width="742" height="681" alt="Screenshot 2026-11-11 000419" src="https://github.com/user-attachments/assets/ddb615de-f39f-4122-a314-3c63582704b0" />



Test d'une requête GET de filtrage combiné par prix minimum et maximum (price_min=100&price_max=500) sur l'API via Postman, retournant un statut 200 OK avec les résultats correspondants (comme la "Wireless Mouse" au prix de 150).


<img width="691" height="596" alt="Screenshot 2026-11-11 000502" src="https://github.com/user-attachments/assets/69df8490-a8b0-4e77-8629-0bbe1ca2ace4" />



Test d'une requête GET de pagination (page=2) sur l'API via Postman, retournant un statut 200 OK avec la liste paginée correspondante à partir du produit "Sneakers".


<img width="893" height="649" alt="Screenshot 2026-11-11 000534" src="https://github.com/user-attachments/assets/840e8ec8-0242-4efd-99bf-c5508c10984d" />




Test d'une requête DELETE sur l'API via Postman pour supprimer un produit spécifique (/api/products/13), retournant un statut 200 OK avec un message de confirmation de suppression ("Product deleted successfully").


<img width="767" height="513" alt="Screenshot 2026-11-11 000618" src="https://github.com/user-attachments/assets/affe5c1c-e031-4d85-9a9e-2a7e6a09e40b" />


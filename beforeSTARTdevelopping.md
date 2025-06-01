# Installation & Configuration de la Plateforme

## 1. Cloner le dépôt

```bash
git clone https://....
cd votre-projet
````

## 2. Copier le fichier d'environnement

### Sous Linux/macOS :

```bash
cp .env.example .env.local
```

### Sous Windows PowerShell :

```powershell
copy .env.example .env.local
```

## 3. Modifier `.env.local` si nécessaire

* Adapter la configuration de la base de données (`DATABASE_URL`)
* Ajouter les clés API Stripe (voir étape Stripe plus bas)

## 4. Installer les dépendances PHP

```bash
composer install
```

## 5. Lancer les migrations

```bash
php bin/console doctrine:migrations:migrate
```

## 6. Lancer le serveur local

```bash
symfony server:start
```

---

## 7. Installer Stripe

```bash
composer require stripe/stripe-php
```

* Créer un compte sur [https://dashboard.stripe.com/test/apikeys](https://dashboard.stripe.com/test/apikeys)
* Ajouter vos clés de test dans `.env.local` :

```
STRIPE_SECRET_KEY=sk_test_***************
STRIPE_PUBLIC_KEY=pk_test_***************
```

---

## 8. Installer MailHog (pour tester les emails en local)

### Sous macOS avec Homebrew :

```bash
brew install mailhog
```

### Sous Linux (via Go) :

```bash
go install github.com/mailhog/MailHog@latest
```

### Suivez ce lien pour l'installation : https://github.com/mailhog/MailHog#installation

### Lancez MailHog (port par défaut : http://localhost:8025)

### Démarrer MailHog :

```bash
mailhog
```

* Accéder à l’interface web : [http://localhost:8025](http://localhost:8025)

* Dans `.env.local`, ajouter :

```
MAILER_DSN=smtp://localhost:1025
```



Configurer Mailer dans `.env.local` :

```
MAILER_DSN=smtp://localhost:1025
```

Charger les fixtures (si nécessaire pour avoir des données de test) :

```bash
php bin/console doctrine:fixtures:load
```

Créer un compte administrateur :

```bash
php bin/console app:create-admin
```

🧪 Accès à l’interface :

* Frontend : [http://localhost:8000](http://localhost:8000)
* MailHog : [http://localhost:8025](http://localhost:8025)

✅ Prêt à tester la plateforme !



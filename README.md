# Projet Veliko

## Inisialation du projet

### Etape 1 : installation des dépendances

Installation des dépendances avec composer (vendor)
```
composer install
```

### Etape 2 : Créez un mail

Pour créer un mail
```
composer require symfony/mailer
```
Pour le messenger 
```
composer require symfony/messenger
```

1) Mettez à jour la variable d'environnement MESSENGER_TRANSPORT_DSN dans .env si nécessaire
et framework.messenger.transports.async dans config/packages/messenger.yaml
2) (si vous utilisez Doctrine) Générez un bin/console de migration Doctrine doctrine:migration:diff
   et exécutez-le bin/console doctrine:migration:migrate
3) Acheminez vos classes de messages vers le transport asynchrone dans config/packages/messenger.yaml.

---> Pour plus d'informations, consultez la documentation : https://symfony.com/doc/current/messenger.html

> ⚠️ **Important:** : Si vous rencontrez cette erreur c'est pas grave continuez à suivre les étapes suivantes:
> "The metadata storage is not up to date, please run the sync-metadata-storage command to fix this issue."

Pour tester l'envoi de mail, vous pouvez créez un controller :
```
symfony console make:controller MailController
```
> ℹ️ **Info:** Verifiez que votre serveur est stoper avec la commande puis lancer le server :

Pour arreter le serveur
```
symfony server:stop
```
Pour lancer le serveur
```
symfony server:start
```
Puis allez sur le web et cherchez :
[Mailtrap](https://mailtrap.io/)

Aller dans :
- **Email testing**
- **Inbox**
- Allez tout en bas et dans **cURL**, choisir **PHP Symfony 5+** et copier le code
- Coller le code dans votre fichier `.env`


Pour tester tout cela allez dans votre controller et juste en dessous de la route ecrivez : 
```
public function index(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        // ...
        return $this->render('mail/index.html.twig', [
            'controller_name' => 'MailController',
        ]);
    }
```
Puis allez sur votre navigateur et tapez : 
```
/mail
```
Si besoin me contactez : 

[GitHub](https://https://github.com/N-Baroukh)
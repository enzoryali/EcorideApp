## Diagramme de Cas d'Utilisation (UML)
```mermaid
graph TD
    %% Acteurs
    Visiteur((Visiteur))
    Utilisateur((Utilisateur))
    Employe((Employé))
    Admin((Administrateur))

    subgraph EcoRide_Platform
        UC1[Rechercher un trajet - US1-3]
        UC2[Créer un compte - US7]
        UC3[Réserver un trajet - US6]
        UC4[Publier un voyage - US9]
        UC5[Gérer véhicules - US8]
        UC6[Valider les avis - US12]
        UC7[Gérer les employés - US13]
        UC8[Consulter Statistiques - US13]
    end

    %% Relations Visiteur
    Visiteur --> UC1
    Visiteur --> UC2
    
    %% Héritage : l'Utilisateur peut faire tout ce que le visiteur fait
    Utilisateur --- Visiteur
    Utilisateur --> UC3
    Utilisateur --> UC4
    Utilisateur --> UC5
    
    %% Employé
    Employe --> UC6
    
    %% Admin
    Admin --> UC7
    Admin --> UC8
```
## Diagramme de Séquence : Réservation (US 6)
```mermaid
sequenceDiagram
    autonumber
    actor U as Utilisateur
    participant F as Front-end (Twig)
    participant C as Contrôleur (Symfony)
    participant B as Base de données (MySQL)

    U->>F: Clique sur "Participer"
    F->>C: Requête POST /reservation/{id}
    
    C->>B: Vérifier places disponibles & crédits
    B-->>C: Données renvoyées
    
    alt Pas assez de crédits ou places
        C-->>F: Message d'erreur
        F-->>U: Affiche "Crédits insuffisants"
    else Validation possible
        C-->>F: Demande double confirmation (US 6)
        U->>F: Confirme la réservation
        F->>C: Validation finale
        
        C->>B: Débiter crédits passager
        C->>B: Diminuer places covoiturage
        C->>B: Enregistrer la réservation
        B-->>C: Succès
        
        C-->>F: Redirection vers l'espace passager
        F-->>U: Affiche "Réservation confirmée"
    end
```
## Diagramme de Classe
```mermaid
classDiagram
    class User {
        -int id
        -string email
        -array roles
        -string password
        -string nom
        -string prenom
        -int credit
        -bool isSuspended
        +getRoles() array
        +getNoteMoyenne() float
        +addCovoiturage(Covoiturage c)
    }

    class Covoiturage {
        -int id
        -DateTime date_depart
        -string lieu_depart
        -int nb_place
        -float prix_personne
        -string statut
        -int duree
        +getDuree() int
        +setStatut(string s)
    }

    class Voiture {
        -int id
        -string modele
        -string immatriculation
        -string energie
        -int nbPlaces
        -bool fumeur
        -bool animaux
        +isFumeur() bool
    }

    class Marque {
        -int id
        -string libelle
        +getLibelle() string
    }

    class Avis {
        -int id
        -string commentaire
        -string note
        -string statut
        +setStatut(string s)
    }

    class Configuration {
        -int id
        +getParametres() Collection
    }

    class Parametre {
        -int id
        -string propriete
        -string valeur
    }

    
    User "1" -- "*" Voiture : possède[cite: 6, 7]
    User "1" -- "*" Configuration : gère[cite: 6, 2]
    User "*" -- "*" Covoiturage : participe/organise[cite: 6, 3]
    User "*" -- "*" Avis : laisse/reçoit[cite: 6, 8]
    Voiture "1" -- "*" Covoiturage : est utilisée pour[cite: 7, 3]
    Marque "1" -- "*" Voiture : fabrique[cite: 4, 7]
    Configuration "1" -- "*" Parametre : contient[cite: 2, 5]
```

# Documentation Technique - EcoRide

## Diagramme de Cas d'Utilisation (UML)
```mermaid
useCaseDiagram
    actor "Visiteur" as V
    actor "Utilisateur" as U
    actor "Employé" as E
    actor "Administrateur" as A

    package "EcoRide Platform" {
        usecase "Rechercher un trajet (US1-3)" as UC1
        usecase "Créer un compte (US7)" as UC2
        usecase "Réserver un trajet (US6)" as UC3
        usecase "Publier un voyage (US9)" as UC4
        usecase "Gérer véhicules (US8)" as UC5
        usecase "Valider les avis (US12)" as UC6
        usecase "Gérer les employés (US13)" as UC7
        usecase "Consulter Statistiques (US13)" as UC8
    }

    V --> UC1
    V --> UC2
    
    U --|> V
    U --> UC3
    U --> UC4
    U --> UC5
    
    E --> UC6
    
    A --> UC7
    A --> UC8

# Organització

A FemPinya fem servir Jira per organitzar les tasques a dur a terme. Els issues de Jira poden tenir les següents etiquetes:

- **BACKLOG**: TOT EL QUE S'HA DE FER. Quan es crea un ticket a Jira, hauria d'anar al backlog. 
- **PER FER**: tickets preparats per ser començats / els següents que s'han de fer
- **EN PROCÉS**: Quan algú està fent aquest ticket
- **ESPERANT APROVACIÓ**: El ticket està fet, existeix una PR amb els canvis, i s'està esperant que un altre desenvolupador s'ho miri.
- **APROVAT**: La PR està aprovada i llesta per testejar.

Fem servir la metodologia Kanban, que a trets generals consisteix en el següent:

- Els tickets van per estats i sempre avancen (sempre cap a la dreta, en general un ticket MAI hauria de saltar a una columna de la seva esquerra).
- Hi ha un màxim de tickets a cada columna. Això facilita que no s'acumulin tickets i no avancem.
- Els tickets s'**autoassignen**. És a dir, cadascú agafa els tickets que pot assumir, no els que els altres li "envien".

# Entorns

Aquí faltarien els estats de deployment (quan està en dev, quan està en prod), pendent de decidir.

# Lint

Abans de obrir una PR es obligatori executar laravel linter:

```
docker-compose exec app ./vendor/bin/pint app/
docker-compose exec app ./vendor/bin/pint tests/
```

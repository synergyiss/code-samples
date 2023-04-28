This is a collection of code samples of Paul Niebuhr

The /command folder has samples of code from the USGA Command System.

All code in this repository are Copyright, 2003 by the USGA and Paul Niebuhr

bll_class.php - a class that contains common business logic for
all tables in the Command system.

contact_individual.php - a class that contains the controller code
for the contact_individual table.  It will use the contact_individual_pl_class
for presentation layer and the contact_individual_bll_class for business
logic.

contact_individual_bll.class.php - a class containing the specific business
logic for the contact_individual table. it inherits the common business
logic from the bll_class. it will use the contact_individual_dal class
to access the perform the data access functions for its table.

contact_individual_dal.class.php - a class containing the data access
logic for the contact individual table. it will inherit common
functionality from the dal_class.

contact_individual_pl.class.php - a class containing presentation logic
for the contact_individual table.  it will inherit common presentation
functionality from the pl_class.

db.class.php - a class that has the base data acccess logic.  it uses
pdo to access MySql databases.

ghin_bll.class.php - a class that consumes the USGA GHIN REST api
to create/update/delete data from the GHIN system.

pl.class.php - a class that contains the common presentation layer
logic.

The /member-intelligence folder contains some of the nextjs code
for the Member Intelligence project.


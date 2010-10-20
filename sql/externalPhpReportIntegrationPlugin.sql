-- This table is necessary for the plugin SendTaskToExternalPhpReportInstance

create table relation_tasks_external_phpreport (
  internalId                integer not null,
  externalId                integer not null,
  constraint pk_relation_tasks_external_phpreport
    primary key (internalId, externalId)
) ;


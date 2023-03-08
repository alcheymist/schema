select 
    debit.date as debit_date,
    debit.amount as debit_amount,
    debit.reason as debit_reason,
    client.name as client_name
from 
    debit
    inner join client on debit.client = client.client
    #
    #Add the table for supporting filtering of the payments from the changes
    inner join changes on changes.pk= debit.debit
where changes.source = 'debit'
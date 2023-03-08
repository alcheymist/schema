select 
    credit.date as credit_date,
    credit.amount as credit_amount,
    credit.reason as credit_reason,
    client.name as client_name
from 
    credit
    inner join client on credit.client = client.client
    #
    #Add the table for supporting filtering of the payments from the changes
    inner join changes on changes.pk= credit.credit
where changes.source = 'credit'
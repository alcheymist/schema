select 
    ebill.due_date as ebill_due_date,
    ebill.current_amount as ebill_current_amount,
    eaccount.num as eaccount_num
from 
    ebill
    inner join eaccount on ebill.eaccount = eaccount.eaccount
    #
    #Add the table for supporting filtering of the payments from the changes
    inner join changes on changes.pk= ebill.ebill
where changes.source = 'ebill'
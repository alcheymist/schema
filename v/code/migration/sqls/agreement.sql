select 
    agreement.start_date as agreement_start_date,
    agreement.duration as agreement_duration,
    agreement.valid as agreement_valid,
    agreement.comment as agreement_comment,
    agreement.terminated as agreement_terminated,
    agreement.review as agreement_review,
    agreement.amount as agreement_amount,
    room.uid as room_uid,
    client.name as client_name
from 
    agreement
    inner join room on agreement.room = room.room
    inner join client on agreement.client = client.client
    #
    #Add the table for supporting filtering of the payments from the changes
    inner join changes on changes.pk= agreement.agreement
where changes.source = 'agreement'
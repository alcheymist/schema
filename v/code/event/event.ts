//
import * as outlook from "../../../outlook/v/code/outlook.js";

import * as server from "./server.js";

//The main class of the event module.
export class plan{
    //
    //Use a unique identifier (for referencing this plan in a database) to create
    //a plan
    constructor(public name:string){}
    //
    //Create a new plan, save and schehdule it on the server. It is an error
    //if the named plan already existing. The return value is either successful
    //or erroneous.
    //If a plan is previously saved, then, re=saving is not necessary. The default
    //is that it is necessary
    async initiate(saved:boolean=true): Promise<void>{
        //
        //Save the activity to the database, if necessary
        if (!saved) this.save();
        //
        //Collect all the scehduleing entries of this plan
        const entries:Array<entry> = [...this.collect_entries()]; 
        //
        //Create the at and crontab schedulers
        const schedulers:Array<scheduler> = [new at_scheduler(entries), new crontab_scheduler(entries)];
        //
        //For each one of them:-
        for(const scheduler of schedulers){
            //
            //Test if it is necessary to refresh the sheduler. If it is refresh
            //teh eduler
            if (scheduler.refresh_is_necessary()) await scheduler.refresh();
        }
    }

    //
    //Collect entries for scheduling
    *collect_entries():Generator<entry>{}
    
    //Save the details in the database.
    protected save(): void;
    
    //
    //Suspend a plan from executeion, to be re-initiated at a later time. It is 
    //an error if the name task is not running.
    cancel(): void;
    
}
//
//The event class represents an event planner..
export class event extends plan{
    //
    constructor(
        //
        //Name of the plan
        name:string,
        //
        //The date when the event is planned to start.
        public start_date: Date = new Date(),
        //
        //The date when the event is planned to end.
        public end_date: Date = new Date('9999-12-31'),
        //
        //An event has one or more activities. The default is none.
        public activities: Array<activity> = []
    ){
        super(name);
    }

    //
    //Collect entries
    *collect_entries():Generator<entry>{
        //
        //Loop through all the activities to yield entries
        for(const activity of this.activities){
            yield *activity.collect_entries();
        }
    }
}
//
//This represents the tasks happening within a certain event.
class activity extends plan{
    //
    //This is linux command or script file (of  such command) to be exceuted 
    //on the server in order to perform the desired activity.
    constructor(
        //
        //Name of the plan
        name:string,
        //
        public cmd: linux_command
    ){
        super(name);
    }
    
}
//
//This represents an activity that is executed on;y once, on the given date
export class once extends activity{
    //
    constructor(
        //
        //Name of the plan
        name:string,
        //
        //The script to execute
        cmd:linux_command,
        //
        public date: Date, 
        //
        public venue?:string
    ){
        super(name, cmd);
    }

    //A perform once activity yields only one entry, if the date is creater or 
    //equal to now
    *collect_entries():Generator<entry>{
        //
        if (this.date.getDate()>=Date.now())
            yield new at_entry(this.cmd, this.date);
    }
    
}
//
//This represents an activity that repeats at rogrammed intervals.
export class repetitive extends activity{
    //
    constructor(
        //
        //Name of the plan
        name:string,
        //
        //The script to execute
        cmd:linux_command,
        //
        //A coded description of when the activity planed to be repeated, 
        //captured as a crontab entry. E.g., 
        //  1 * * * *
        //describes an activity repeats daily in the 1st minute of every hour
        public frequency: string,
        //
        //The date/time when the activity is planned to start. The default is now
        public start_date: Date = new Date(),
        //
        //The end date/time when the activity is planned to end. The defaut is end of time
        public end_date: Date = new Date(outlook.view.end_of_time)
    ){
        super(name, cmd);
    }

    //A repetitive even yields at most 3 entries: 2 at and one crontab
    //entries
    *collect_entries():Generator<entry>{
        if (this.start_date>=new Date()) 
            yield new at_entry('refresh_crontab.php', this.start_date);
        //    
        if (this.end_date>=new Date()) 
            yield new at_entry('refresh_crontab.php', this.end_date);
        //    
        if (this.start_date<new Date() && this.end_date>new Date()) 
            yield new crontab_entry(this.cmd, this.frequency);
    }
    
   
}

type linux_command = string; 

//
class entry{
    constructor(public cmd:linux_command){}
}

class at_entry extends entry{
    constructor(cmd:linux_command, public date:Date){
        super(cmd);
    }
}


class crontab_entry extends entry{
    constructor(cmd:linux_command, public frequency:string){
        super(cmd);
    }
}

class scheduler{

    //
    constructor(public entries:Array<entry>){}
    //
    //Returns true if it is necessary to refresh theh scehduler
    refresh_is_necessary():boolean{
        return this.entries.length>0 
    }

    //Refresh the scehduler
    refresh():void{
        //
        //Compile the sql for retriving entries fronm the database
        //
        //Retrieve the entries
        //
        //Execute the entries

    }
}

class at_scheduler extends scheduler{
    //
    //Crontab entries
    declare public entries:Array<at_entry>;
    //
    constructor(entries:Array<entry>){
        const myentries = entries.filter(entry=>entry instanceof  at_entry)
        super(myentries);
        
    }

    //Refresh the at jobs
    async refresh(): Promise<void> {
        //
        await server.exec('at_scheduler', [], 'refresh', [])
    }

}

class crontab_scheduler extends scheduler{
    //
    //Crontab entries
    declare public entries:Array<crontab_entry>;
    //
    constructor(entries:Array<entry>){
        const myentries = entries.filter(entry=>entry instanceof  crontab_entry)
        super(myentries);
        
    }
    //
    //Refresh the crontab
    async refresh(): Promise<void> {
        //
        await server.exec('crontab_scheduler', [], 'refresh', []);
    }
}

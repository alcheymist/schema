"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || function (mod) {
    if (mod && mod.__esModule) return mod;
    var result = {};
    if (mod != null) for (var k in mod) if (k !== "default" && Object.prototype.hasOwnProperty.call(mod, k)) __createBinding(result, mod, k);
    __setModuleDefault(result, mod);
    return result;
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.repetitive = exports.once = exports.event = exports.plan = void 0;
//
const outlook = __importStar(require("../../../outlook/v/code/outlook.js"));
const server = __importStar(require("./server.js"));
//The main class of the event module.
class plan {
    //
    //Use a unique identifier (for referencing this plan in a database) to create
    //a plan
    constructor(name) {
        this.name = name;
    }
    //
    //Create a new plan, save and schehdule it on the server. It is an error
    //if the named plan already existing. The return value is either successful
    //or erroneous.
    //If a plan is previously saved, then, re=saving is not necessary. The default
    //is that it is necessary
    async initiate(saved = true) {
        //
        //Save the activity to the database, if necessary
        if (!saved)
            this.save();
        //
        //Collect all the scehduleing entries of this plan
        const entries = [...this.collect_entries()];
        //
        //Create the at and crontab schedulers
        const schedulers = [new at_scheduler(entries), new crontab_scheduler(entries)];
        //
        //For each one of them:-
        for (const scheduler of schedulers) {
            //
            //Test if it is necessary to refresh the sheduler. If it is refresh
            //teh eduler
            if (scheduler.refresh_is_necessary())
                await scheduler.refresh();
        }
    }
    //
    //Collect entries for scheduling
    *collect_entries() { }
}
exports.plan = plan;
//
//The event class represents an event planner..
class event extends plan {
    //
    constructor(
    //
    //Name of the plan
    name, 
    //
    //The date when the event is planned to start.
    start_date = new Date(), 
    //
    //The date when the event is planned to end.
    end_date = new Date('9999-12-31'), 
    //
    //An event has one or more activities. The default is none.
    activities = []) {
        super(name);
        this.start_date = start_date;
        this.end_date = end_date;
        this.activities = activities;
    }
    //
    //Collect entries
    *collect_entries() {
        //
        //Loop through all the activities to yield entries
        for (const activity of this.activities) {
            yield* activity.collect_entries();
        }
    }
}
exports.event = event;
//
//This represents the tasks happening within a certain event.
class activity extends plan {
    //
    //This is linux command or script file (of  such command) to be exceuted 
    //on the server in order to perform the desired activity.
    constructor(
    //
    //Name of the plan
    name, 
    //
    cmd) {
        super(name);
        this.cmd = cmd;
    }
}
//
//This represents an activity that is executed on;y once, on the given date
class once extends activity {
    //
    constructor(
    //
    //Name of the plan
    name, 
    //
    //The script to execute
    cmd, 
    //
    date, 
    //
    venue) {
        super(name, cmd);
        this.date = date;
        this.venue = venue;
    }
    //A perform once activity yields only one entry, if the date is creater or 
    //equal to now
    *collect_entries() {
        //
        if (this.date.getDate() >= Date.now())
            yield new at_entry(this.cmd, this.date);
    }
}
exports.once = once;
//
//This represents an activity that repeats at rogrammed intervals.
class repetitive extends activity {
    //
    constructor(
    //
    //Name of the plan
    name, 
    //
    //The script to execute
    cmd, 
    //
    //A coded description of when the activity planed to be repeated, 
    //captured as a crontab entry. E.g., 
    //  1 * * * *
    //describes an activity repeats daily in the 1st minute of every hour
    frequency, 
    //
    //The date/time when the activity is planned to start. The default is now
    start_date = new Date(), 
    //
    //The end date/time when the activity is planned to end. The defaut is end of time
    end_date = new Date(outlook.view.end_of_time)) {
        super(name, cmd);
        this.frequency = frequency;
        this.start_date = start_date;
        this.end_date = end_date;
    }
    //A repetitive even yields at most 3 entries: 2 at and one crontab
    //entries
    *collect_entries() {
        if (this.start_date >= new Date())
            yield new at_entry('refresh_crontab.php', this.start_date);
        //    
        if (this.end_date >= new Date())
            yield new at_entry('refresh_crontab.php', this.end_date);
        //    
        if (this.start_date < new Date() && this.end_date > new Date())
            yield new crontab_entry(this.cmd, this.frequency);
    }
}
exports.repetitive = repetitive;
//
class entry {
    constructor(cmd) {
        this.cmd = cmd;
    }
}
class at_entry extends entry {
    constructor(cmd, date) {
        super(cmd);
        this.date = date;
    }
}
class crontab_entry extends entry {
    constructor(cmd, frequency) {
        super(cmd);
        this.frequency = frequency;
    }
}
class scheduler {
    //
    constructor(entries) {
        this.entries = entries;
    }
    //
    //Returns true if it is necessary to refresh theh scehduler
    refresh_is_necessary() {
        return this.entries.length > 0;
    }
    //Refresh the scehduler
    refresh() {
        //
        //Compile the sql for retriving entries fronm the database
        //
        //Retrieve the entries
        //
        //Execute the entries
    }
}
class at_scheduler extends scheduler {
    //
    constructor(entries) {
        const myentries = entries.filter(entry => entry instanceof at_entry);
        super(myentries);
    }
    //Refresh the at jobs
    async refresh() {
        //
        await server.exec('at_scheduler', [], 'refresh', []);
    }
}
class crontab_scheduler extends scheduler {
    //
    constructor(entries) {
        const myentries = entries.filter(entry => entry instanceof crontab_entry);
        super(myentries);
    }
    //
    //Refresh the crontab
    async refresh() {
        //
        await server.exec('crontab_scheduler', [], 'refresh', []);
    }
}
//# sourceMappingURL=event.js.map
exports.printUsageThenQuit = function(){
    console.log("\nUsage: nodejs server.js <DomainName> <PortNumber>");
    console.log("DomainName: Domain primary key for SQL");
    console.log("PortNumber: Port to listen on");
    process.exit(1)
};

exports.isInvalidInput = function(argumentArray){
    if(argumentArray.length < 4){
        console.log("Error: not enough arguments");
        return true;
    }
    if( argumentArray[2] === null){
        console.log("Error: Domain is null");
        return true;
    }
    var portNumberSuspect = parseInt(argumentArray[3]);
    var typeOfPortNumArg = typeof portNumberSuspect;

    if(typeOfPortNumArg != "number"){
        console.log("Error: portnumber is not a number");
        return true;
    }
    if(portNumberSuspect < 5000){
        console.log("Error: portnumber has to be > 5000");
        return true;
    }


    return false;
}


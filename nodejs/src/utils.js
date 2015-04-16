exports.printUsageThenQuit = function(){
    console.log("\nUsage: nodejs server.js <DomainName> <PortNumber>");
    console.log("DomainName: Domain primary key for SQL");
    console.log("PortNumber: Port to listen on");
    process.exit(1)
};


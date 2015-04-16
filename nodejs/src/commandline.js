



program = require('commander');
program.acceptConnectionsFrom = '127.0.0.1';


program
    .version('0.0.5')
    .option('-dev, --dev', 'Activate Developer Options')
    .arguments('<domain> <port>')
    .action(function (domain, port){
        program.domainName = domain;
        program.portNumber = parseInt(port);
    })
    .parse(process.argv);

if(program.dev){
    program.acceptConnectionsFrom = '0.0.0.0';
    console.log("Dev mode active: accepting all incoming connections");
}

if(typeof program.portNumber != "number"){
    console.log("Error: Portnumber is not a number");
    program.outputHelp();
    process.exit(1);
}
if(program.portNumber < 5000){
    console.log("Error: Portnumber has to be greater than 5000");
    program.outputHelp();
    process.exit(1);
}


module.exports = program;






program = require('commander');


program
    .version('0.0.5')
    .option('-dev, --dev', 'Activate Developer Options')
    .parse(process.argv);

if(program.dev){
    program.acceptConnectionsFrom = '0.0.0.0';
}


module.exports = program;


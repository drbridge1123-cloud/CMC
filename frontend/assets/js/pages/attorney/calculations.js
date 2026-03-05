/**
 * Attorney Case Calculations
 * Pure calculation helpers for settlement fee/commission math.
 * Used by templates and modal handlers.
 */

function calcLegalFee(settled) {
    return (parseFloat(settled) || 0) / 3;
}

function calcDemandCommission(dlf) {
    return (parseFloat(dlf) || 0) * 0.05;
}

function calcLitCommission(dlf, rate) {
    return (parseFloat(dlf) || 0) * ((parseFloat(rate) || 0) / 100);
}

function getLitFeeRate(resType) {
    const group40  = ['Arbitration Award', 'Beasley'];
    const groupVar = ['Co-Counsel', 'Other'];
    if (group40.includes(resType))  return 40;
    if (groupVar.includes(resType)) return null;
    return 33.33;
}

function isVariableType(resType) {
    return ['Co-Counsel', 'Other'].includes(resType);
}

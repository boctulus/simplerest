const ProperInput = require('./ProperInput');

describe('ProperInput', () => {
    let properInput;

    beforeEach(() => {
        properInput = new ProperInput();
    });

    test('Caso 1: Básico (min: 0, max: 100, step: 1)', () => {
        expect(properInput.processValue(50, 0, 100, 1)).toBe(50);
        expect(properInput.processValue(-1, 0, 100, 1)).toBe(0);
        expect(properInput.processValue(101, 0, 100, 1)).toBe(100);
        expect(properInput.processValue(50.4, 0, 100, 1)).toBe(50);
        expect(properInput.processValue(50.5, 0, 100, 1)).toBe(51);
    });

    test('Caso 2: Decimales (min: 0, max: 10, step: 0.1)', () => {
        expect(properInput.processValue(5.15, 0, 10, 0.1)).toBe(5.2);
        expect(properInput.processValue(-0.1, 0, 10, 0.1)).toBe(0);
        expect(properInput.processValue(10.1, 0, 10, 0.1)).toBe(10);
    });

    test('Caso 3: Negativo a Positivo (min: -50, max: 50, step: 5)', () => {
        expect(properInput.processValue(0, -50, 50, 5)).toBe(0);
        expect(properInput.processValue(-52, -50, 50, 5)).toBe(-50);
        expect(properInput.processValue(52, -50, 50, 5)).toBe(50);
        expect(properInput.processValue(2, -50, 50, 5)).toBe(0);
        expect(properInput.processValue(3, -50, 50, 5)).toBe(5);
    });

    test('Caso 4: Solo Mínimo (min: 100, step: 10)', () => {
        expect(properInput.processValue(105, 100, NaN, 10)).toBe(110);
        expect(properInput.processValue(99, 100, NaN, 10)).toBe(100);
    });

    test('Caso 5: Solo Máximo (max: 1000, step: 50)', () => {
        expect(properInput.processValue(975, NaN, 1000, 50)).toBe(1000);
        expect(properInput.processValue(1001, NaN, 1000, 50)).toBe(1000);
        expect(properInput.processValue(5, NaN, 1000, 50)).toBe(0);
    });

    test('Caso 6: Paso Grande (min: 0, max: 1000000, step: 10000)', () => {
        expect(properInput.processValue(15000, 0, 1000000, 10000)).toBe(20000);
        expect(properInput.processValue(4999, 0, 1000000, 10000)).toBe(0);
        expect(properInput.processValue(1000001, 0, 1000000, 10000)).toBe(1000000);
    });

    test('Caso 7: Decimales Pequeños (min: 0, max: 1, step: 0.01)', () => {
        expect(properInput.processValue(0.505, 0, 1, 0.01)).toBe(0.51);
        expect(properInput.processValue(-0.01, 0, 1, 0.01)).toBe(0);
        expect(properInput.processValue(1.01, 0, 1, 0.01)).toBe(1);
    });

    test('Caso 8: Rango Estrecho (min: 99, max: 101, step: 0.1)', () => {
        expect(properInput.processValue(100.05, 99, 101, 0.1)).toBe(100.1);
        expect(properInput.processValue(98.9, 99, 101, 0.1)).toBe(99);
        expect(properInput.processValue(101.1, 99, 101, 0.1)).toBe(101);
    });

    test('Caso 9: Solo Step (step: 3.14)', () => {
        expect(properInput.processValue(3.22, NaN, NaN, 3.14)).toBeCloseTo(3.14, 2);
        expect(properInput.processValue(4.71, NaN, NaN, 3.14)).toBeCloseTo(4.71, 2);
    });

    test('Caso 10: Valores Grandes (min: 1000000, max: 9999999, step: 111111)', () => {
        expect(properInput.processValue(1500000, 1000000, 9999999, 111111)).toBe(1555555);
        expect(properInput.processValue(999999, 1000000, 9999999, 111111)).toBe(1000000);
        expect(properInput.processValue(10000000, 1000000, 9999999, 111111)).toBe(9999999);
    });

    test('Caso 11: Min y Step con decimales (min: 25.5, step: 8.5)', () => {
        expect(properInput.processValue(30, 25.5, NaN, 8.5)).toBe(34);
        expect(properInput.processValue(25, 25.5, NaN, 8.5)).toBe(25.5);
        expect(properInput.processValue(44, 25.5, NaN, 8.5)).toBe(42.5);
    });
});
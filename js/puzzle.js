// js/puzzle.js
// Solvable sliding puzzle engine (3x3, 4x4, 5x5) by shuffling via valid moves.

(function () {
  const Puzzle = {
    _size: 4,
    _tiles: [],
    _moves: 0,
    _initial: [],
    _emptyIndex: 0,

    reset(n) {
      this._size = n;
      const total = n * n;
      this._tiles = [];
      for (let i = 1; i < total; i++) this._tiles.push(i);
      this._tiles.push(0);
      this._moves = 0;
      this._emptyIndex = total - 1;
      this._initial = this._tiles.slice();
      return this.getState();
    },

    getState() {
      return {
        size: this._size,
        tiles: this._tiles.slice(),
      };
    },

    getMoves() {
      return this._moves;
    },

    restoreInitial() {
      this._tiles = this._initial.slice();
      this._moves = 0;
      this._emptyIndex = this._tiles.indexOf(0);
    },

    isSolved() {
      const n = this._size;
      const total = n * n;
      for (let i = 0; i < total - 1; i++) {
        if (this._tiles[i] !== i + 1) return false;
      }
      return this._tiles[total - 1] === 0;
    },

    _neighborsOfEmpty() {
      const n = this._size;
      const e = this._emptyIndex;
      const r = Math.floor(e / n);
      const c = e % n;
      const res = [];
      if (r > 0) res.push(e - n);
      if (r < n - 1) res.push(e + n);
      if (c > 0) res.push(e - 1);
      if (c < n - 1) res.push(e + 1);
      return res;
    },

    _swap(i, j) {
      const t = this._tiles[i];
      this._tiles[i] = this._tiles[j];
      this._tiles[j] = t;
    },

    tryMove(tileValue) {
      const n = this._size;
      const idx = this._tiles.indexOf(tileValue);
      if (idx < 0) return false;

      const e = this._emptyIndex;
      const r1 = Math.floor(idx / n), c1 = idx % n;
      const r2 = Math.floor(e / n), c2 = e % n;
      const man = Math.abs(r1 - r2) + Math.abs(c1 - c2);
      if (man !== 1) return false;

      this._swap(idx, e);
      this._emptyIndex = idx;
      this._moves += 1;
      return true;
    },

    shuffle(steps = 120) {
      // Make solvable by performing random valid moves from solved state.
      this._moves = 0;

      let lastEmpty = -1;
      for (let k = 0; k < steps; k++) {
        const neighbors = this._neighborsOfEmpty();
        // Avoid undoing the previous move too often
        const filtered = neighbors.filter((x) => x !== lastEmpty);
        const pickFrom = filtered.length ? filtered : neighbors;
        const choice = pickFrom[Math.floor(Math.random() * pickFrom.length)];

        lastEmpty = this._emptyIndex;
        this._swap(choice, this._emptyIndex);
        this._emptyIndex = choice;
      }

      // Save this as the reset point
      this._initial = this._tiles.slice();
      this._moves = 0;
    },
  };

  window.Puzzle = Puzzle;
})();

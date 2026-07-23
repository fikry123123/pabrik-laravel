<div class="flex gap-2 items-center resep-row">
    <select name="id_bahan[]" class="flex-1 min-w-0 p-3 border bg-slate-50 rounded-xl" required>
        <option value="">-- Pilih Bahan Baku --</option>
        @foreach($materials as $b)
        <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->satuan }})</option>
        @endforeach
    </select>
    <input type="number" step="0.1" name="qty_butuh[]"
           class="w-20 sm:w-28 flex-shrink-0 p-3 border bg-slate-50 rounded-xl" placeholder="Qty" required>
    <div class="w-12 flex-shrink-0"></div>
</div>

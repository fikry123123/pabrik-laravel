<div class="flex gap-3 items-center resep-row">
    <select name="id_bahan[]" class="flex-1 p-3 border bg-slate-50 rounded-xl" required>
        <option value="">-- Pilih Bahan Baku --</option>
        @foreach($materials as $b)
        <option value="{{ $b->id }}">{{ $b->nama }} ({{ $b->satuan }})</option>
        @endforeach
    </select>
    <input type="number" step="0.1" name="qty_butuh[]"
           class="w-32 p-3 border bg-slate-50 rounded-xl" placeholder="Butuh Qty" required>
    <div class="w-[50px]"></div>
</div>

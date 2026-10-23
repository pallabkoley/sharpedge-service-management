<tr data-asset="{{ $index }}" data-name="{{ strtolower($asset->asset_name ?? '') }}">
    <td style="text-align:center;font-weight:500;color:var(--text-mid);">{{ $index + 1 }}</td>
    <td class="sticky-col" style="background:inherit;position:sticky;left:0;z-index:2;text-align:left;padding-left:6px;">
        <span class="td-label">{{ $asset->asset_name ?? 'New Asset' }}</span>
    </td>
    <td><span style="font-size:10px;font-weight:500;color:var(--text-mid);">{{ $asset->block ?? 'N/A' }}</span></td>
    <td><input class="td-amt" type="text" id="opening_{{ $index }}" value="{{ fmtRaw($asset->opening_wdv ?? 0) }}" oninput="onRowChange({{ $index }})" /></td>
    <td><input class="td-amt" type="text" id="addition_{{ $index }}" value="{{ fmtRaw($asset->purchase_amount ?? 0) }}" oninput="onRowChange({{ $index }})" /></td>
    <td><input class="td-amt" type="text" id="revaluation_{{ $index }}" value="{{ fmtRaw($asset->revaluation ?? 0) }}" oninput="onRowChange({{ $index }})" style="color:var(--slate-light);" /></td>
    <td><input class="td-amt" type="text" id="sale_{{ $index }}" value="{{ fmtRaw($asset->sale_amount ?? 0) }}" oninput="onRowChange({{ $index }})" /></td>
    <td><input class="td-amt" type="date" id="putToUse_{{ $index }}" value="{{ $asset->purchase_date ?? $asset->put_to_use_date ?? '' }}" oninput="onRowChange({{ $index }})" style="font-size:9px;padding:2px;text-align:center;" /></td>
    <td><input class="td-amt" type="date" id="saleDate_{{ $index }}" value="{{ $asset->sale_date ?? '' }}" oninput="onRowChange({{ $index }})" style="font-size:9px;padding:2px;text-align:center;" /></td>
    <td><input class="td-amt readonly" type="text" id="depBase_{{ $index }}" value="{{ fmtRaw((($asset->opening_wdv ?? 0) + ($asset->purchase_amount ?? 0) + ($asset->revaluation ?? 0) - ($asset->sale_amount ?? 0))) }}" readonly /></td>
    <td><input class="td-amt readonly" type="text" id="depAmt_{{ $index }}" value="0" readonly /></td>
    <td><input class="td-amt readonly" type="text" id="closingWDV_{{ $index }}" value="0" readonly /></td>
    <td>
        <button class="btn-breakup" onclick="openBreakupModal({{ $index }})">
            <i class="bi bi-pencil"></i> —
        </button>
    </td>
    <td><button class="btn-erp btn-ghost btn-sm" onclick="deleteAssetRow({{ $index }})" style="font-size:10px;padding:2px 6px;color:var(--red);"><i class="bi bi-trash"></i></button></td>
</tr>
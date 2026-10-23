<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, Tahoma, Verdana, sans-serif; font-size: 11px; background: #c8c8c8; color: #000; min-height: 100vh; }
    #toolbar { background: #2c2c2c; color: #fff; padding: 4px 10px; display: flex; align-items: center; gap: 10px; position: sticky; top: 0; z-index: 100; border-bottom: 2px solid #555; }
    #toolbar .app-title { font-size: 12px; font-weight: bold; color: #ffdd44; letter-spacing: 0.5px; margin-right: 10px; }
    #toolbar button { background: #444; color: #fff; border: 1px solid #666; padding: 3px 10px; font-size: 11px; cursor: pointer; font-family: Arial, sans-serif; border-radius: 3px; }
    #toolbar button:hover { background: #555; }
    #toolbar button.active { background: #0054a6; border-color: #0054a6; }
    #toolbar .save-status { margin-left: auto; font-size: 10px; color: #aaffaa; font-style: italic; }
    #toolbar .sep { color: #666; }
    #sheet-wrapper { width: 100%; padding: 12px; min-height: calc(100vh - 32px); }
    #computation-sheet { width: 1100px; margin: 0 auto; background: #fff; border: 2px solid #333; }
    .sheet-header { border-bottom: 2px solid #000; padding: 8px 0 6px; text-align: center; }
    .sheet-header .firm-name { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #000; padding-bottom: 4px; margin-bottom: 4px; }
    .sheet-header .main-title { font-size: 15px; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 3px; }
    .sheet-header .sub-title { font-size: 11px; font-weight: bold; }
    .header-meta { display: flex; justify-content: center; gap: 40px; margin-top: 4px; font-size: 11px; }
    .header-meta span strong { font-weight: bold; }
    .master-block { border-bottom: 2px solid #000; }
    .master-block-title { background: #2c2c2c; color: #fff; font-weight: bold; text-transform: uppercase; padding: 3px 8px; font-size: 11px; letter-spacing: 0.5px; }
    .master-grid { display: grid; grid-template-columns: 1fr 1fr; border-top: 1px solid #999; }
    .master-col { border-right: 1px solid #999; }
    .master-col:last-child { border-right: none; }
    .master-row { display: grid; grid-template-columns: 180px 1fr; border-bottom: 1px solid #ddd; min-height: 22px; }
    .master-row:last-child { border-bottom: none; }
    .master-label { background: #f5f5f5; padding: 2px 6px; border-right: 1px solid #ccc; font-size: 10.5px; display: flex; align-items: center; color: #333; }
    .master-val { padding: 1px 4px; display: flex; align-items: center; }
    .master-val input, .master-val select { width: 100%; border: none; background: transparent; font-size: 11px; font-family: Arial, sans-serif; font-weight: bold; color: #000; padding: 1px 2px; outline: none; }
    .master-val input:focus { background: #fffde0; border: 1px solid #0054a6; }
    .master-val input[readonly] { background: #f0f0f0; color: #333; font-weight: normal; }
    .master-val select { appearance: none; -webkit-appearance: none; background: transparent; cursor: pointer; font-weight: bold; }
    .master-val select:focus { background: #fffde0; border: 1px solid #0054a6; }
    .comp-table { width: 100%; border-collapse: collapse; border-bottom: 1px solid #999; }
    .comp-table td, .comp-table th { border: 1px solid #bbb; padding: 2px 6px; vertical-align: middle; }
    .rn { width: 28px; text-align: center; color: #888; font-size: 10px; background: #f0f0f0; border-right: 1px solid #ccc !important; }
    .row-section td { background: #2c2c2c; color: #fff; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 8px; border-color: #555 !important; }
    .row-section .rn { background: #1a1a1a; color: #aaa; }
    .row-subsection td { background: #4a4a6a; color: #fff; font-weight: bold; font-size: 10.5px; text-transform: uppercase; padding: 3px 8px; border-color: #666 !important; }
    .row-subsection .rn { background: #3a3a5a; color: #ccc; }
    .row-normal td { background: #fff; font-size: 11px; }
    .row-normal:nth-child(even) td { background: #fafafa; }
    .row-normal:hover td { background: #fffde0; }
    .row-calc td { background: #f0f4f0; font-weight: bold; font-size: 11px; }
    .row-calc input { background: #e8ece8 !important; font-weight: bold; }
    .row-subtotal td { background: #e0e8f4; font-weight: bold; font-size: 11px; border-top: 2px double #555 !important; }
    .row-subtotal .amt { font-size: 11.5px; }
    .row-grandtotal td { background: #d0dce8; font-weight: bold; font-size: 12px; border-top: 3px double #000 !important; border-bottom: 3px double #000 !important; }
    .row-note td { font-style: italic; color: #555; font-size: 10px; background: #fafafa; padding: 1px 8px; }
    .row-less td { background: #fff8f0; font-size: 11px; color: #660000; }
    .row-final td { background: #1a3a5c; color: #fff; font-weight: bold; font-size: 12px; border-color: #000 !important; }
    .row-final .rn { background: #0f2a4a; color: #aaa; }
    .row-final input { color: #fff; background: #1a3a5c !important; font-weight: bold; font-size: 12px; }
    .inp { width: 100%; border: none; background: transparent; font-size: 11px; font-family: Arial, sans-serif; color: #000; padding: 1px 3px; outline: none; text-align: right; }
    .inp:focus { background: #fffde0; border: 1px solid #0054a6; outline: none; }
    .inp-text { text-align: left; }
    .inp[readonly] { background: #f0f0f0; color: #333; font-weight: bold; }
    .tds-table { width: 100%; border-collapse: collapse; }
    .tds-table th { background: #3a3a6a; color: #fff; font-size: 10px; font-weight: bold; padding: 3px 5px; border: 1px solid #555; text-align: center; text-transform: uppercase; }
    .tds-table td { border: 1px solid #bbb; padding: 2px 4px; font-size: 10.5px; }
    .tds-table tr:nth-child(even) td { background: #f5f8ff; }
    .tds-table .tds-total td { background: #e0e8f4; font-weight: bold; border-top: 2px double #555; }
    .tds-table input { width: 100%; border: none; background: transparent; font-size: 10.5px; font-family: Arial; padding: 0 2px; outline: none; }
    .tds-table input:focus { background: #fffde0; }
    .collapse-btn { cursor: pointer; background: none; border: none; color: #aaa; font-size: 10px; padding: 0 4px; float: right; }
    .collapse-btn:hover { color: #fff; }
    .dyn-btn { background: #444; color: #fff; border: 1px solid #666; padding: 1px 8px; font-size: 9px; cursor: pointer; border-radius: 2px; }
    .dyn-btn:hover { background: #555; }
    .dyn-btn.del { background: #663333; border-color: #883333; }
    .dyn-btn.del:hover { background: #884444; }
    .dyn-btn.add { background: #2c5c2c; border-color: #3a7a3a; }
    .dyn-btn.add:hover { background: #3a7a3a; }
    .action-cell { white-space: nowrap; }
    .barcode-area { border-top: 2px solid #000; padding: 10px 15px; background: #f9f9f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .barcode-box { border: 1px solid #888; padding: 8px 20px; background: #fff; font-family: 'Courier New', monospace; font-size: 14px; letter-spacing: 2px; min-width: 200px; text-align: center; }
    .barcode-box img { max-height: 50px; }
    .barcode-upload { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .barcode-upload button { background: #444; color: #fff; border: 1px solid #666; padding: 2px 10px; font-size: 10px; cursor: pointer; }
    .barcode-upload button:hover { background: #555; }
    .barcode-upload input[type="file"] { display: none; }
    .declaration-area { border-top: 2px solid #000; padding: 10px 15px; background: #fafafa; }
    .declaration-text { font-size: 10px; font-style: italic; border: 1px solid #999; padding: 6px; margin-bottom: 8px; background: #fff; line-height: 1.6; }
    .sig-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 8px; }
    .sig-block { border: 1px solid #bbb; padding: 6px; }
    .sig-block .sig-title { font-weight: bold; font-size: 10px; text-transform: uppercase; border-bottom: 1px solid #ccc; margin-bottom: 4px; padding-bottom: 2px; }
    .sig-block .sig-row { display: flex; gap: 6px; margin-bottom: 3px; font-size: 10px; }
    .sig-block .sig-row label { color: #555; min-width: 90px; }
    .sig-block .sig-row input { flex: 1; border: none; border-bottom: 1px dotted #999; font-size: 10px; font-family: Arial; outline: none; background: transparent; }
    .sig-space { height: 30px; border-bottom: 1px solid #000; margin: 8px 0 2px; }
    .sig-label { font-size: 9px; text-align: center; color: #555; }
    .print-footer { border-top: 1px solid #ccc; padding: 4px 10px; font-size: 9px; color: #666; display: flex; justify-content: space-between; background: #f5f5f5; }
    .table-col-header th { background: #3a5a3a; color: #fff; font-size: 10px; font-weight: bold; padding: 3px 6px; border: 1px solid #666 !important; text-align: center; text-transform: uppercase; }
    .amt { text-align: right; }
    .ctr { text-align: center; }
    @media print { body { background: #fff; } #toolbar { display: none; } #sheet-wrapper { padding: 0; } #computation-sheet { width: 100%; border: none; } .collapse-btn { display: none; } }
    #statusbar { background: #1a1a1a; color: #ccc; font-size: 10px; padding: 2px 10px; display: flex; gap: 20px; position: fixed; bottom: 0; left: 0; right: 0; z-index: 100; border-top: 1px solid #444; }
    #statusbar span { color: #ffdd44; }

    /* Modal specific styles */
    .salary-modal-table th { background: #3a5a3a; color: #fff; font-size: 10px; font-weight: bold; padding: 3px 5px; border: 1px solid #555; text-align: center; text-transform: uppercase; }
    .salary-modal-table td { border: 1px solid #bbb; padding: 2px 4px; font-size: 10.5px; }
    .salary-modal-table input { width: 100%; border: none; background: transparent; font-size: 10.5px; font-family: Arial; padding: 0 2px; outline: none; }
    .salary-modal-table input:focus { background: #fffde0; }
    .salary-modal-table .modal-total td { background: #e0e8f4; font-weight: bold; border-top: 2px double #555; }
    .modal-dialog { max-width: 900px; }
    .modal-content { font-size: 11px; }
</style>